<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\WalletCharge as WalletChargeModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned as FeedPinnedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment as FeedCommentModel;

class PennedController extends Controller
{
    /**
     * App
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create the controller instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
    }

    /**
     * 申请动态置顶.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function feedPinned(Request $request, ResponseContract $response, FeedModel $feed)
    {
        $user = $request->user();

        if ($feed->user_id !== $user->id) {
            return $response->json(['message' => ['你没有权限申请']])->setStatusCode(403);
        } elseif ($feed->pinned()->where('user_id', $user->id)->first()) {
            return $response->json(['message' => ['已经申请过']])->setStatusCode(422);
        }

        $pinned = new FeedPinnedModel();
        $pinned->user_id = $user->id;
        $pinned->channel = 'feed';
        $pinned->target = $feed->id;

        return $this->app->call([$this, 'validateBase'], [
            'pinned' => $pinned,
            'call' => function (WalletChargeModel $charge, FeedPinnedModel $pinned) use ($user, $feed) {
                $charge->user_id = $user->id;
                $charge->channel = 'user';
                $charge->account = $user->id;
                $charge->action = 0;
                $charge->amount = $pinned->amount;
                $charge->subject = '动态申请置顶';
                $charge->body = sprintf('申请置顶动态《%s》', str_limit($feed->feed_content, 100));
                $charge->status = 1;

                return $this->app->call([$this, 'save'], [
                    'charge' => $charge,
                    'pinned' => $pinned,
                ]);
            }
        ]);
    }

    /**
     * 保存所有数据库记录.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Plus\Models\WalletCharge $charge
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned $pinned
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function save(Request $request, ResponseContract $response, WalletChargeModel $charge, FeedPinnedModel $pinned)
    {
        $user = $request->user();
        $user->getConnection()->transaction(function () use ($user, $charge, $pinned) {
            $user->wallet()->decrement('balance', $charge->amount);
            $user->walletCharges()->save($charge);
            $pinned->save();
        });

        return $response->json(['message' => ['申请成功']])->setStatusCode(201);
    }

    /**
     * 基础验证.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned $pinned
     * @param callable $call
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function validateBase(Request $request, FeedPinnedModel $pinned, callable $call)
    {
        $user = $request->user();
        $rules = [
            'amount' => [
                'required',
                'integer',
                'min:1',
                'max:'.$user->wallet->balance,
            ],
            'day' => [
                'required',
                'integer',
                'min:1'
            ]
        ];
        $messages = [
            'amount.required' => '请输入申请金额',
            'amount.integer' => '参数有误',
            'amount.min' => '输入金额有误',
            'amount.max' => '余额不足',
            'day.required' => '请输入申请天数',
            'day.integer' => '天数只能为整数',
            'day.min' => '输入天数有误',
        ];
        $this->validate($request, $rules, $messages);

        $pinned->amount = intval($request->input('amount'));
        $pinned->day = intval($request->input('day'));

        return $this->app->call($call, [
            'pinned' => $pinned,
        ]);
    }
}
