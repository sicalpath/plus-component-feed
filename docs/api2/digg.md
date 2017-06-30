# 赞/喜欢 接口

- [获取赞列表](#获取赞列表)
- [动态点赞/喜欢](#动态点赞/喜欢)
- [取消赞/喜欢](#取消赞/喜欢)

## 获取赞列表

```
GET /feeds/:feed/diggs
```

#### Response

```
Status: 200 OK
```
```json5
[
    1 // 点赞用户ID
]
```

## 动态点赞/喜欢

```
POST /feeds/:feed/diggs
```

#### Response

```
Status: 201 Created
```
```json
{
    "message": [
        "成功"
    ]
}
```

## 取消赞/喜欢

```
DELETE /feeds/:feed/undigg
```

#### Response

```
Status: 204 No Centent
```
