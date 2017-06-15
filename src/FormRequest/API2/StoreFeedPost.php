<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;

class StoreFeedPost extends FormRequest
{
    /**
     * authorization check.
     *
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function authorize(): bool
    {
        // 检查认证用户所在用户组是否有发送分享权限.
        return $this->user()->can('feed-post');
    }

    /**
     * get the validator rules.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function rules(): array
    {
        return [
            'feed_title' => 'nullable',
            'feed_content' => ['required_without:storage_task'],
            'feed_from' => 'required|numeric|in:1,2,3,4,5',
            'feed_mark' => 'required|unique:feeds,feed_mark',
            'storage_task' => ['required_without:feed_content', 'array'],
            'storage_task.*.id' => ['required_with:storage_task', 'distinct', 'exists:storage_tasks,id'],
            'storage_task.*.amount' => ['required_with:storage_task.*.type', 'integer'],
            'storage_task.*.type' => ['required_with:storage_task.*.amount', 'string', 'in:read,download'],
            'feed_latitude' => 'required_with:feed_longtitude,feed_geohash',
            'feed_longtitude' => 'required_with:feed_latitude,feed_geohash',
            'feed_geohash' => 'required_with:feed_latitude,feed_longtitude',
            'amount' => 'nullable|integer',
        ];
    }

    /**
     * Get the validator rule messages.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function messages(): array
    {
        return [
            'feed_content.required_without' => '没有发送任何内容',
            'feed_from.required' => '没有发送设备标识',
            'feed_from.in' => '设备标识不在允许范围',
            'feed_mark.required' => '请求非法',
            'feed_mark.unique' => '请求的内容已存在',
            'storage_task.required_without' => '没有发送任何内容',
            'storage_task.*.id.required_with' => '储存任务ID必须存在',
            'storage_task.*.id.distinct' => '请求的ID包含重复的值',
            'storage_task.*.id.exists' => '储存任务不存在',
            'storage_task.*.amount.integer' => '附件收费参数错误',
            'feed_latitude.required_with' => '位置标记不完整',
            'feed_longtitude.required_with' => '位置标记不完整',
            'feed_geohash.required_with' => '位置标记不完整',
            'amount.integer' => '动态收费参数错误',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException('你没有发布动态权限');
    }
}
