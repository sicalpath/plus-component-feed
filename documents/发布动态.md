# 发布动态

## 接口地址

```
/api/v1/feeds
```
## 请求方法

```
POST
```
### HTTP Status Code

201

##请求字段

| name     | type     | must     | description |
|----------|:--------:|:--------:|:--------:|
| feed_content  | string      | yes      | 动态文字内容 |
| feed_title | string   | no    | 动态标题 |
| latitude   | string     | no    | 纬度|
| longtitude	| string	| no	| 经度	|
| geohash	|	string	| no	| GEOhash值	|
| storage_task_ids	| array	| no 	| 动态相关图片id	|
| feed_from	| number	| yes | 1:pc 2:h5 3:ios 4:android 5:其他 |
| feed_mark	| int	| yes | 移动端标记 |
| isatuser	| int	| yes | 是否有@用户内容 1-有@用户内容  |

## 发布动态@人规则

数据格式：`[tsplus:{uid}:feed]`

接口接收到参数 `isatuser` = 1时 将解析动态的内容并取出其中的数据，处理并通知对应uid的用户

返回数据时将原样返回，需要客户端自行处理

## 返回体

```json5
{
  "status": true,
  "code": 0,
  "message": "动态创建成功",
  "data": 1,
}
```
```
code请参见[Feed消息对照表](Feed消息对照表.md)
```

