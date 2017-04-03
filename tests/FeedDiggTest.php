<?php

namespace  Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\tests;

use Tests\TestCase;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Models\AuthToken;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class FeedDiggTest extends TestCase
{
    protected $user;
    protected $auth;

    protected function setUp()
    {
        parent::setUp();

        // register user.
        $this->user = User::create([
            'phone'    => '1878'.rand(1111111, 9999999),
            'name'     => 'ts'.rand(1000, 9999),
            'password' => bcrypt(123456),
        ]);

        // set token info.
        $this->auth = new AuthToken();
        $this->auth->token = md5(str_random(32));
        $this->auth->refresh_token = md5(str_random(32));
        $this->auth->expires = 0;
        $this->auth->state = 1;

        // save token.
        $this->user->tokens()->save($this->auth);

        $this->feed = new Feed();
        $this->feed->feed_content = '这是一条garbage数据';
        $this->feed->feed_client_id = '127.0.0.1';
        $this->feed->user_id = $this->user->id;
        $this->feed->feed_from = 4;
        $this->feed->feed_latitude = '';
        $this->feed->feed_longtitude = '';
        $this->feed->feed_geohash = '';
        $this->feed->feed_title = '123';
        $this->feed->save();
    }

    /**
     * 卸载方法，清理测试后的冗余数据.
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function tearDown()
    {
        $this->feed->forceDelete();
        $this->user->forceDelete();
        $this->auth->forceDelete();
        parent::tearDown();
    }

    /**
     * 测试点赞情况.
     *
     * @author bs<414606094@qq.com>
     * @return [type] [description]
     */
    public function testAddDigg()
    {
        $uri = '/api/v1/feeds/'.$this->feed->id.'/digg';
        $responseHeader = ['ACCESS-TOKEN' => $this->auth->token];
        $responseBody = [];
        $response = $this->postJson($uri, $responseBody, $responseHeader);
        $response->assertStatus(201);

        $json = [
            'status' => true,
            'message' => '点赞成功',
        ];
        $response->assertJson($json);
    }

    /**
     * 测试重复点赞情况.
     *
     * @author bs<414606094@qq.com>
     * @return [type] [description]
     */
    public function testAddRepeatDigg()
    {
        $uri = '/api/v1/feeds/'.$this->feed->id.'/digg';
        $responseHeader = ['ACCESS-TOKEN' => $this->auth->token];
        $responseBody = [];
        $this->postJson($uri, $responseBody, $responseHeader);
        $response = $this->postJson($uri, $responseBody, $responseHeader);
        $response->assertStatus(400);

        $json = [
            'code' => 6005,
            'status' => false,
            'message' => '已赞过该动态',
        ];
        $response->assertJson($json);
    }

    /**
     * 测试正常取消点赞的情况.
     *
     * @author bs<414606094@qq.com>
     * @return [type] [description]
     */
    public function testCancelDigg()
    {
        $uri = '/api/v1/feeds/'.$this->feed->id.'/digg';
        $responseHeader = ['ACCESS-TOKEN' => $this->auth->token];
        $responseBody = [];
        $this->postJson($uri, $responseBody, $responseHeader);
        $response = $this->deleteJson($uri, $responseBody, $responseHeader);
        $response->assertStatus(204);
    }

    /**
     * 测试重复取消点赞的情况.
     *
     * @author bs<414606094@qq.com>
     * @return [type] [description]
     */
    public function testCancelRepeatDigg()
    {
        $uri = '/api/v1/feeds/'.$this->feed->id.'/digg';
        $responseHeader = ['ACCESS-TOKEN' => $this->auth->token];
        $responseBody = [];
        $this->postJson($uri, $responseBody, $responseHeader);
        $this->deleteJson($uri, $responseBody, $responseHeader);
        $response = $this->deleteJson($uri, $responseBody, $responseHeader);
        $response->assertStatus(400);

        $json = [
                'code' => 6006,
                'status' => false,
                'message' => '未对该动态点赞',
        ];
        $response->assertJson($json);
    }
}
