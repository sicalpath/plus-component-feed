<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2;

use Illuminate\Validation\Rule;
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
            'feed_content' => ['required_without:files'],
            'feed_from' => 'required|numeric|in:1,2,3,4,5',
            'feed_mark' => 'required|unique:feeds,feed_mark',
            'feed_latitude' => 'required_with:feed_longtitude,feed_geohash',
            'feed_longtitude' => 'required_with:feed_latitude,feed_geohash',
            'feed_geohash' => 'required_with:feed_latitude,feed_longtitude',
            'amount' => 'nullable|integer',
            'files' => ['required_without:feed_content', 'array'],
            'files.*.id' => [
                'required_with:files',
                'distinct',
                Rule::exists('file_withs', 'id')->where(function ($query) {
                    $query->where('channel', null);
                    $query->where('raw', null);
                }),
            ],
            'files.*.amount' => ['required_with:files.*.type', 'integer'],
            'files.*.type' => ['required_with:files.*.amount', 'string', 'in:read,download'],
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
            'files.*.amount.integer' => '文件请求参数不合法',
            'feed_latitude.required_with' => '位置标记不完整',
            'feed_longtitude.required_with' => '位置标记不完整',
            'feed_geohash.required_with' => '位置标记不完整',
            'amount.integer' => '动态收费参数错误',
            'files.required_without' => '没有发生任何内容',
            'files.*.id.required_without' => '发送的文件不存在',
            'files.*.id.distinct' => '发送的文件中存在重复内容',
            'files.*.id.exists' => '文件不存在或已经被使用',
            'files.*.type.required_with' => '文件请求参数不完整',
            'files.*.type.string' => '文件请求参数类型错误',
            'files.*.type.in' => '文件请求类型错误',
            'files.*.amount.required_with' => '文件请求参数类型错误',
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
