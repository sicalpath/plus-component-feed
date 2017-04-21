# 对一条动态点赞的用户列表

## 接口地址

/api/v1/feeds/{feed_id}/diggusers

## 请求方法

GET

## 额外请求参数（get传入）

limit 请求数据条数  默认10条
max_id 用来翻页的记录id(对应数据体里的feed_digg_id)

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
      "feed_digg_id": 2,
      "user_id": 1
    },
    {
      "feed_digg_id": 7,
      "user_id": 2
    }
  ]
}
```

## 返回字段

| name     | type     | must     | description |
|----------|:--------:|:--------:|:--------:|
| feed_digg_id  | int      | yes      | 点赞记录id |
| user_id | int	  | yes		 | 点赞用户id|

code请参见[Feed消息对照表](Feed消息对照表.md)