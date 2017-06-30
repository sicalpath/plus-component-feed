# 置顶

- [动态置顶](#动态置顶)
- [评论置顶](#评论置顶)

## 动态置顶

```
POST /feeds/:feed/pinned
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
POST /feeds/:feed/comments/:comment/pinned
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
