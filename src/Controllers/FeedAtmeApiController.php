<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedAtme;

class FeedAtmeApiController extends Controller
{
	/**
	 * 获取@我的分享列表
	 *  
	 * @author bs<414606094@qq.com>
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getAtmeList(Request $request)
	{

		$user = $request->user()->id;
		$limit = intval($request->input('limit')) ? : 10;
		$FeedAtModel = new FeedAtme();
		$list = $FeedAtModel::ByAtUserId($user)->take($limit);
		if (!empty(intval($request->input('max_id')))) {
			$max_id = intval($request->input('max_id'));
			$list = $list->ByMaxId($max_id);
		}

		if ($list = $list->orderBy('atme_id', 'desc')->get()) {
			foreach ($list as $key => $value) {
				if ($value->feed) {
					$data['feed_id'] = $value->feed->feed_id;
					$data['feed_title'] = $value->feed->feed_title;
					$data['feed_content'] = $value->feed->feed_content;
					$data['created_at'] = $value->feed->created_at->timestamp;
					if ($value->user) {
						$data['user']['user_name'] = $value->user->name;
						$data['user']['phone'] = $value->user->phone;
					}
				}
				$datas[] = $data;
			}

            return response()->json(static::createJsonData([
                'code'   => 0,
                'status' => true,
                'data' => $datas,
            ]))->setStatusCode(200);
		}

        return response()->json(static::createJsonData([
            'code'   => 0,
            'status' => true,
            'data' => [],
        ]))->setStatusCode(200);
	}
}