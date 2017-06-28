# 动态评论

- [发布评论](#发布评论)
- [获取评论](#获取评论)
- [评论列表](#评论列表)
- [删除评论](#删除评论)

## 发布评论

```
POST /feeds/:feed/comments
```

### Input

| 名称 | 类型 | 描述 |
|:----:|:----:|----|
| reply_to_user_id | Integer | 可选，回复评论的用户 ID |
| comment_content | Steing | 必须，评论内容 |
| comment_mark | Integer | 必须，评论标记 |

#### Response

```
Status: 201 Created
```

```json5
{
    "message": "评论成功",
    "id": 5 // 评论ID
}
```

## 获取评论

```
GET /feeds/:feed/comments/:comment
```

#### Response

```
Status: 200 OK
```

```json5
{
    "id": 1, // 评论ID
    "user_id": 1, // 评论用户
    "to_user_id": 1, // 客户端无需知道用处，反正客户端用不到（一般是动态发布者ID）
    "reply_to_user_id": 0, // 回复的用户
    "feed_id": 1, // 动态ID
    "comment_content": "我是第一条评论", // 评论内容
    "comment_mark": 1, // 评论标记
    "pinned": 0, // 是否是被固定（置顶）的评论
    "created_at": "2017-06-27 07:56:26", // 评论事件
    "updated_at": "2017-06-27 07:56:26" // 更新时间
}
```

## 评论列表

```
GET /feeds/:feed/comments
```

### Parameters

| 名称 | 类型 | 描述 |
|:----:|:----:|----|
| limit | Integer | 可选，获取评论条数 |
| after | Integer | 可选，上次请求数据的最后一条，或者请求该 Feed ID 之后的数据 |

#### Response

```
Status: 200 OK
```
```json5
{
    "comments": [
        {...} // 内容数据参考「获取评论」内容数据
    ],
    "pinned": []
}
```

> `pinned` 列表和 `comments` 列表一致，表述置顶列表数据.

## 删除评论

```
DELETE /feeds/:feed/comments/:comment
```

#### Response

```
Status: 204 No Content
```
