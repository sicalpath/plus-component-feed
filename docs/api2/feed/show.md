# 获取动态

- [单条](#单条)
- [批量](#批量)

## 单条

```
GET /feeds/:feed
```

#### Response

```
Status: 201 OK
```
```json5
{
    "id": 13,
    "created_at": "2017-06-21 01:54:52",
    "updated_at": "2017-06-21 01:54:52",
    "deleted_at": null,
    "user_id": 1, // 发布动态的用户
    "feed_content": "动态内容", // 内容
    "feed_from": 2,
    "feed_digg_count": 0, // 点赞数
    "feed_view_count": 0, // 查看数
    "feed_comment_count": 0, // 评论数
    "feed_latitude": null, //  纬度
    "feed_longtitude": null, // 经度
    "feed_geohash": null, // GeoHash
    "audit_status": 1, // 审核状态
    "feed_mark": 12,
    "has_digg": true, // 是否点赞
    "has_collect": false, // 用户是否收藏当前动态
    "paid_node": {
        "paid": true, // 当前用户是否已经付费
        "node": 9, // 付费节点
        "amount": 20 // 付费金额
    },
    "comment_paid_node": { // 评论收费信息.
        "paid": true,
        "node": 11,
        "amount": 50
    },
    "images": [ // 图片
        {
            "file": 4, // 文件 file_with 标识 不收费图片只存在 file 这一个字段。
            "size": null, // 图像尺寸，非图片为 null，图片没有尺寸也为 null，
            "amount": 100, // 收费多少
            "type": "download", // 收费方式
            "paid": false, // 当前用户是否购买
            "paid_node": 10 付费节点
        },
        {
            "file": 5,
            "size": '1930x1930' // 当图片有尺寸的时候采用 width x height 格式返回。
        }
    ],
    "diggs": [ // 点赞用户列表，里面都是用户ID.
        1
    ]
}
```

##### Not paid

```json5
{
    "message": [
        "请购买动态"
    ],
    "paid_node": 9, // 付费节点
    "amount": 20 // 动态价格
}
```

## 批量

```
GET /feeds
```

### Parameters

| 名称 | 类型 | 描述 |
|:----:|:----:|----|
| limit | Integer | 可选，默认值 20 ，获取条数 |
| after | Integer | 可选，上次获取到数据最后一条 ID，用于获取该 ID 之后的数据。 |
| type | String | 可选，默认值 new，可选值 `new` 、`hot` 和 `follow` |

> 列表为倒叙

#### Response

```
Status: 200 OK
```
```json5
[
    "ad": [...] // 没有广告也可能是 null
    "pinned": [] // 没有置顶也有可能是 null，内容和 feeds 一致.
    "feeds": [
        {...}  // 数据参考 单条内容
    ]
]
```

> `feed_content` 字段在列表中，如果是收费动态则只返回 100 个文字。
