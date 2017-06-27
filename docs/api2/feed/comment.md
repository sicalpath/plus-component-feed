# 动态评论

- [发布评论](#发布评论)
- [获取评论](#获取评论)
- [评论列表](#评论列表)

## 发布评论

## 获取评论

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
