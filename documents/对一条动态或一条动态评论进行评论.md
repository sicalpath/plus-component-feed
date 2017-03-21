# 对一条动态或一条动态评论进行评论

## 接口地址

/api/v1/feeds/{feed_id}/comment

## 请求方法

POST

### HTTP Status Code

201

## 请求字段

| name     | type     | must     | description |
|----------|:--------:|:--------:|:--------:|
| comment_content | string   | yes    | 评论内容 |
| reply_to_user_id     | int     | no    | 被评论者id 对评论进行评论时传入|
| comment_mark | int  | yes      | 移动端标记 |

## 返回体

```json5
{
  "status": true,
  "code": 0,
  "message": "评论成功",
  "data": 1,
}
```
code请参见[Feed消息对照表](Feed消息对照表.md)