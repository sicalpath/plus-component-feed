# 根据id获取评论列表

## 接口地址

/api/v1/feeds/comments

## 请求方法

GET

## 请求参数

comment_ids 评论id  以逗号隔开或者数组形式传入

### HTTP Status Code

200

## 返回体

```json5
{
  "status": true,
  "code": 0,
  "message": "操作成功",
  "data": [
    {
      "id": 1,
      "created_at": "2017-03-02 08:14:13",
      "comment_content": "123123",
      "user_id": 1,
      "to_user_id": 1,
      "reply_to_user_id": 0,
      "comment_mark": 0
    }
  ]
}
```

## 返回字段

| name     | type     | must     | description |
|----------|:--------:|:--------:|:--------:|
| id  | string      | yes      | 评论id |
| created_at | string	  | yes		 | 评论时间 |
| comment_content     | string  	  | yes 	 | 评论内容 |
| user_id     | int      | yes    | 评论者id |
| to_user_id     | int      | yes    | 动态作者id |
| reply_to_user_id     | int      | yes    | 被回复者id |
| comment_mark | int  | yes    | 评论的移动端标记 |

code请参见[Feed消息对照表](Feed消息对照表.md)