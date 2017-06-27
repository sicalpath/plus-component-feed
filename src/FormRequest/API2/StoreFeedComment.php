<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedComment extends FormRequest
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
            'reply_to_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'comment_content' => ['required', 'string', 'max:500'],
            'comment_mark' => ['required', 'unique:feed_comments,comment_mark'],
        ];
    }

    /**
     * Get the validator messages.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function messages(): array
    {
        return [
            'reply_to_user_id.integer' => '回复用户类型错误',
            'reply_to_user_id.exists' => '回复的用户不存在',
            'comment_content.required' => '回复内容不能为空',
            'comment_content.string' => '回复内容必须是字符串',
            'comment_content.max' => '回复内容过长',
            'comment_mark.required' => '回复标记必须存在',
            'comment_mark.unique' => '请勿发送重复内容',
        ];
    }
}
