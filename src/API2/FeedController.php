<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\FileWith as FileWithModel;
use Zhiyi\Plus\Models\PaidNode as PaidNodeModel;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed as FeedRepository;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services\FeedCount as FeedCountService;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedPost as StoreFeedPostRequest;

class FeedController extends Controller
{
    /**
     * 分享列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, ApplicationContract $app)
    {
        $type = $request->query('type', 'new');

        if (! in_array($type, ['new', 'hot', 'follow'])) {
            $type = 'new';
        }

        return $app->call([$this, $type]);
    }

    /**
     * Get new feeds.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feedModel
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function new(Request $request, FeedModel $feedModel, FeedRepository $repository)
    {
        $limit = $request->query('limit', 20);
        $after = $request->query('after');

        $feeds = $feedModel->with('paidNode')
        ->where(function ($query) use ($after) {
            if (! $after) {
                return;
            }

            $query->where('id', '<', $after);
        })->limit($limit)
        ->orderBy('id', 'desc')
        ->get();

        $user = $request->user('api')->id ?? 0;

        return $feedModel->getConnection()->transaction(function () use ($feeds, $repository, $user) {
            return $feeds->map(function (FeedModel $feed) use ($repository, $user) {
                $repository->setModel($feed);
                $repository->images();
                $repository->hasDigg($user);
                $repository->infoDiggUsers();
                $feed->has_collect = $feed->collected($user);
                $repository->format($user);

                if ($feed->paid === false) {
                    $feed->feed_content = str_limit($feed->feed_content, 100, '');
                }

                return $feed;
            });
        });
    }

    public function hot()
    {
        // todo.
    }

    public function follow()
    {
        // todo.
    }

    /**
     * 获取动态详情.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @param int $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(Request $request, FeedRepository $repository, int $feed)
    {
        $user = $request->user('api')->id ?? 0;
        $feed = $repository->find($feed);

        if ($feed->paidNode !== null && $feed->paidNode->paid($user) === false) {
            return response()->json([
                'message' => ['请购买动态'],
                'paid_node' => $feed->paidNode->id,
                'amount' => $feed->paidNode->amount,
            ])->setStatusCode(403);
        }

        // 启用获取事物，避免多次 sql 查询造成查询连接过多.
        return $feed->getConnection()->transaction(function () use ($feed, $repository, $user) {
            $feed->has_collect = $feed->collected($user);
            $repository->images();
            $repository->hasDigg($user);
            $repository->infoDiggUsers();

            return $repository->format($user);
        });
    }

    /**
     * 储存分享.
     *
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedPost $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(StoreFeedPostRequest $request)
    {
        $user = $request->user();
        $feed = $this->fillFeedBaseData($request, new FeedModel());

        $paidNodes = $this->makePaidNode($request);
        $fileWiths = $this->makeFileWith($request);

        try {
            $feed->saveOrFail();
            $feed->getConnection()->transaction(function () use ($request, $feed, $paidNodes, $fileWiths, $user) {
                $this->saveFeedPaidNode($request, $feed);
                $this->saveFeedFilePaidNode($paidNodes, $feed);
                $this->saveFeedFileWith($fileWiths, $feed);
                app(FeedCountService::class)->count($user->id, 'feeds_count', 'increment', 1); // 增加用户分享数量.
            });
        } catch (\Exception $e) {
            $feed->delete();
            throw $e;
        }

        return response()->json(['message' => ['发布成功'], 'id' => $feed->id])->setStatusCode(201);
    }

    /**
     * 创建文件使用模型.
     *
     * @param StoreFeedPostRequest $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function makeFileWith(StoreFeedPostRequest $request)
    {
        return FileWithModel::whereIn(
            'id',
            collect($request->input('images'))->filter(function (array $item) {
                return isset($item['id']);
            })->map(function (array $item) {
                return $item['id'];
            })->values()
        )->where('channel', null)
        ->where('raw', null)
        ->where('user_id', $request->user()->id)
        ->get();
    }

    /**
     * 创建付费节点模型.
     *
     * @param StoreFeedPostRequest $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function makePaidNode(StoreFeedPostRequest $request)
    {
        return collect($request->input('images'))->filter(function (array $item) {
            return isset($item['amount']);
        })->map(function (array $item) {
            $paidNode = new PaidNodeModel();
            $paidNode->channel = 'file';
            $paidNode->raw = $item['id'];
            $paidNode->amount = $item['amount'];
            $paidNode->extra = $item['type'];

            return $paidNode;
        });
    }

    /**
     * 保存分享图片使用.
     *
     * @param array $fileWiths
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function saveFeedFileWith($fileWiths, FeedModel $feed)
    {
        foreach ($fileWiths as $fileWith) {
            $fileWith->channel = 'feed:image';
            $fileWith->raw = $feed->id;
            $fileWith->save();
        }
    }

    /**
     * 保存分享文件付费节点.
     *
     * @param array $nodes
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function saveFeedFilePaidNode($nodes, FeedModel $feed)
    {
        foreach ($nodes as $node) {
            $node->subject = '购买动态附件';
            $node->body = sprintf('购买动态《%s》的图片', str_limit($feed->feed_content, 100, '...'));
            $node->save();
        }
    }

    /**
     * 保存分享付费节点.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function saveFeedPaidNode(Request $request, FeedModel $feed)
    {
        $amount = $request->input('amount');

        if (! $amount) {
            return;
        }

        $paidNode = new PaidNodeModel();
        $paidNode->amount = $amount;
        $paidNode->channel = 'feed';
        $paidNode->raw = $feed->id;
        $paidNode->subject = sprintf('购买动态《%s》', str_limit($feed->feed_content, 100, '...'));
        $paidNode->body = $paidNode->subject;
        $paidNode->save();
    }

    /**
     * 填充分享初始数据.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function fillFeedBaseData(Request $request, FeedModel $feed): FeedModel
    {
        foreach ($request->only(['feed_content', 'feed_from', 'feed_mark', 'feed_latitude', 'feed_longtitude', 'feed_geohash']) as $key => $value) {
            $feed->$key = $value;
        }

        $feed->feed_client_id = $request->ip();
        $feed->audit_status = 1;
        $feed->user_id = $request->user()->id;

        return $feed;
    }
}
