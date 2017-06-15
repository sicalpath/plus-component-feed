# 动态发布

```
POST /feeds
```

### Input

| Name | Type | Description |
|:----:|:----:|----|
| feed_title | string | 分享标题 |
| feed_content | string | 分享内容，如果存在附件，则为可选，否则必须存在 |
| feed_from | integer | 客户端标识，1-PC、2-Wap、3-iOS、4-android、5-其他 |
| feed_mark | mixed | 客户端请求唯一标识 |
| storage_task | array | 结构：`{ id: <id>, amount: <amount> }`，amount 为可选，id 必须存在，amount 为收费金额，单位分 |
| feed_latitude | string | 纬度，当经纬度， GeoHash 任意一个存在，则本字段必须存在 |
| feed_longtitude | string | 纬度，当经纬度， GeoHash 任意一个存在，则本字段必须存在 |
| feed_geohash | string | GeoHash，当经纬度， GeoHash 任意一个存在，则本字段必须存在 |
| amount | inteter | 应用收费，不存在表示不收费，存在表示手费。|


### Example
```json5
{
    "feed_title": "<title>",
    "feed_content": "<content>",
    "feed_from": "<from>",
    "feed_mark": "<mark>",
    "storage_task": [
        {
            "id": "<id:1>"
        },
        {
            "id": "<id:2>",
            "amount": "<amount>"
        }
    ],
    "feed_latitude": "<latitude>",
    "feed_longtitude": "<longtitude>",
    "feed_geohash": "<geohash>",
    "amount": "<amount>"
}
```

### Response

```
Status: 201 Created
```
```json5
{
    "message": [
        "发布成功"
    ],
    "id": 1
}
```
