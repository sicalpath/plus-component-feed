<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services;
use JPush\Client;

class Feedpush 
{
	public function __construct()
	{
		$appkey = env('JPUSH_APP_KEY');
		$secret = env('JPUSH_MASTER_SECRET');
		if (!$appkey || !$secret) {
			return false;
		}

		$this->client = new Client($appkey, $secret);
	}

	public function push($alert, $alias, $extras = array())
	{
        $extras = array_merge($extras, ['type' => 'feed']);

        $notification = array(
            'extras' => $extras, 
        );

        try {

            $result = $this->client->push()
                ->setOptions(1, null, null, false, null)
                ->setPlatform('all') //全部平台
                ->addAlias($alias) // 指定用户
                ->iosNotification($alert, $notification)
                ->androidNotification($alert, $notification)
                ->send();
            return [
                'code'  => 1,
                'data' => [],
                'message' => '推送成功',
            ]; 
        } catch (\Exception $e) {
            return [
                'code'  => 0,
                'message' => $e->getMessage(),
            ]; 
        }
	}
}