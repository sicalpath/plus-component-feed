# 我收到的赞列表

## 接口地址

/api/v1/feeds/diggmes

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
      "id": 1,
      "created_at": "2017-03-08 10:16:40",
      "feed_id": 1,
      "feed_content": "4444",
      "feed_title": "123",
      "storages": [
        {
          "feed_storage_id": 1
        }
      ]
    }
  ]
}
```

## 返回字段

| name     | type     | must     | description |
|----------|:--------:|:--------:|:--------:|
| id       | string   | yes      | 赞id |
| created_at | string	| yes		   | 评论时间 |
| feed_id  | int      | yes      | 相关动态id |
| feed_content | string | yes    | 动态内容 |
| storages | array    | no       | 动态相关附件 |
| feed_storage_id | int | no     | 附件id |


code请参见[Feed消息对照表](Feed消息对照表.md)