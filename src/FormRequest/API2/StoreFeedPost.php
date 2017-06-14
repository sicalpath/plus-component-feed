<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2;

use Illuminate\Foundation\Http\FormRequest;

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
        return $this->user() ? true : false;
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
            'storage_task.*.id' => ['required_with:storage_task', 'exists:storage_tasks,id'],
            'latitude' => 'required_with:longtitude,geohash',
            'longtitude' => 'required_with:latitude,geohash',
            'geohash' => 'required_with:latitude,longtitude',
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
            'storage_task.*.id.exists' => '储存任务不存在',
            'latitude.required_with' => '位置标记不完整',
            'longtitude.required_with' => '位置标记不完整',
            'geohash.required_with' => '位置标记不完整',
        ];
    }
}
