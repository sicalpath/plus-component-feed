# 我收到的评论列表

## 接口地址

/api/v1/feeds/commentmes

## 请求方法

GET

## 额外请求参数（get传入）

limit 请求数据条数  默认15条
max_id 用来翻页的记录id(对应数据体里的id)

### HTTP Status Code

200

## 返回体

```json5
{
  "status": true,
  "code": 0,
  "message": "获取成功",
  "data": [
    {
      "id": 2,
      "created_at": "2017-03-08 10:09:38",
      "updated_at": "2017-03-08 10:09:39",
      "deleted_at": null,
      "user_id": 1,
      "to_user_id": 1,
      "reply_to_user_id": 0,
      "feed_id": 1,
      "comment_content": "12312313",
      "feed": {
        "id": 1,
        "created_at": "2017-03-02 16:16:46",
        "user_id": 1,
        "feed_content": "4444",
        "feed_title": "123",
        "storages": [
          {
            "feed_storage_id": 1,
            "pivot": {
              "feed_id": 1,
              "feed_storage_id": 1,
              "created_at": "2017-03-08 11:38:33",
              "updated_at": "2017-03-08 11:38:34"
            }
          }
        ]
      }
    }
  ]
}
```

## 返回字段

| name     | type     | must     | description |
|----------|:--------:|:--------:|:--------:|
| id       | string   | yes      | 评论id |
| created_at | string	| yes		   | 评论时间 |
| comment_content | string | yes | 评论内容 |
| user_id  | int      | yes      | 评论者id |
| to_user_id | int    | yes      | 动态作者id |
| reply_to_user_id | int | yes   | 被回复者id |
| feed_id  | int      | yes      | 相关动态id |
| feed     | array    | yes      | 动态相关信息 |
| feed_content | string | yes    | 动态内容 |
| storages | array    | no       | 动态相关附件 |
| feed_storage_id | int | no     | 附件id |


code请参见[Feed消息对照表](Feed消息对照表.md)