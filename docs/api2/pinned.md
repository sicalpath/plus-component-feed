# 置顶

- [动态置顶](#动态置顶)
- [评论置顶](#评论置顶)
- [动态评论置顶审核列表](#动态评论置顶审核列表)
- [评论置顶审核通过](#评论置顶审核通过)
- [拒绝动态评论置顶申请](#拒绝动态评论置顶申请)
- [删除动态置顶评论](#删除动态置顶评论)

## 动态置顶

```
POST /feeds/:feed/pinneds
```

#### Input

| 名称 | 类型 | 描述 |
|:----:|:----:|----|
| amount | Integer | 必须，置顶总价格，单位分。 |
| day | Integer | 必须，置顶天数。|

#### Response

```
Status: 201 Created
```
```json
{
    "message": [
        "申请成功"
    ]
}
```

## 评论置顶

```
POST /feeds/:feed/comments/:comment/pinneds
```

#### Input

| 名称 | 类型 | 描述 |
|:----:|:----:|----|
| amount | Integer | 必须，置顶总价格，单位分。 |
| day | Integer | 必须，置顶天数。|

#### Response

```
Status: 201 Created
```
```json
{
    "message": [
        "申请成功"
    ]
}
```

## 动态评论置顶审核列表

```
GET /user/feed-comment-pinneds
```

### Parameters

| 名称 | 类型 | 描述 |
|:----:|:----:|----|
| limit | Integer | 获取的条数, 默认 20 |
| after | Integer | 上次请求列表倒叙最后一条 ID |

#### Response

```
Status: 200 OK
```
```json5
[
    // ...
    {
        "id": 2, // 置顶 ID
        "amount": 10, // 置顶金额
        "day": 1, // 置顶天数
        "user_id": 1, // 申请置顶用户
        "expires_at": "2017-07-05 08:29:49", // 置顶过期时间，如果待审核状态，此值为 null
        "created_at": "2017-06-30 12:04:15", // 申请时间
        "comment": { // 如果为 null 表示评论已被删除
            "id": 2, // 评论 ID
            "content": "我是第2条评论", // 评论内容
            "pinned": true, // 是否已置顶
            "user_id": 1, // 评论者用户
            "reply_to_user_id": 0, // 被回复用户
            "created_at": "2017-06-27 08:59:14" // 评论时间
        },
        "feed": { // 如果为 null 评论被删了，获取不到。或者动态被删。
            "id": 1, // 动态 ID
            "content": "动态内容" // 动态内容
        }
    }
]
```

## 评论置顶审核通过

```
PATCH /feeds/:feed/comments/:comment/pinneds/:pinned
```

#### Response

```
Status: 201 Created
```
```json
{
    "message": [
        "置顶成功"
    ]
}
```

## 拒绝动态评论置顶申请

```
DELETE /user/feed-comment-pinneds/:pinned
```

#### Response

```
Status: 204 No Centent
```

## 删除动态置顶评论

```
DELETE /feeds/:feed/comments/:comment/unpinned
```

#### Response

```
Status: 204 No Centent
```
