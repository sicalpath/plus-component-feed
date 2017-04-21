# 获取单条动态信息

## 接口地址

/api/v1/feeds/{feed_id}

## 请求方法

GET

### HTTP Status Code

200

## 返回体

```json5
{
  "status": true,
  "code": 0,
  "message": "获取动态成功",
  "data": {
    "user_id": 1,
    "feed": {
      "id": 1,
      "title": "title1",
      "content": "feed_content1",
      "created_at": "2017-03-02 08:14:13",
      "feed_from": 2,
      "feed_storages": [
        1
      ]
    },
    "tool": {
      "digg": 0,
      "view": 0,
      "comment": 0
    },
    "comments": [],
    "diggs": [
      1,
      2
    ]
  }
}
```
code请参见[Feed消息对照表](Feed消息对照表.md)