# @我的动态列表

## 接口地址

/api/v1/feeds/atmes

## 请求方法

GET

## 额外请求参数（get传入）

limit 请求数据条数  默认10条
max_id 用来翻页的记录id(对应数据体里的id)

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
      "id": 2,
      "created_at": "2017-03-07 11:35:06",
      "updated_at": "2017-03-07 11:35:06",
      "at_user_id": 1,
      "user_id": 1,
      "feed_id": 1
    }
  ]
}
```

## 返回字段

| name          | type     | must     | description |
|---------------|:--------:|:--------:|:--------:|
| feed_id       | int      | yes      | 动态id |
| feed_title    | string	 | yes		  | 动态标题 |
| feed_content  | string   | yes 	    | 动态内容 |
| created_at    | string   | yes      | 发布时间 |
| user_id       | int      | yes      | @我的用户id |

code请参见[Feed消息对照表](Feed消息对照表.md)