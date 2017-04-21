# 获取动态列表

## 接口地址
## 最新：

/api/v1/feeds  
## 关注：

/api/v1/feeds/follows 
## 热门：

/api/v1/feeds/hots  
## 某个用户发布的动态列表：

/api/v1/feeds/users/{user_id}  

## 我的收藏列表：

/api/v1/feeds/collections  

## 额外请求参数

limit 请求数据条数  默认10条

max_id 用来翻页的记录id(对应数据体里的feed_id ,最新和关注选填)

feed_ids 查询动态的id (最新接口里选填)

page (页码  热门选填)

## 请求方法

GET

### HTTP Status Code

200

## 返回体

```json5
{
    "user_id": 5,
    "feed_mark": 0,
    "feed": {
        "feed_id": 1,
        "title": "标题",
        "content": "内容",
        "created_at": "2017-03-02 08:14:13",
        "user_id": 2,
        "feed_from": 1, //[1:pc 2:h5 3:ios 4:android 5:其他]

        "storage": [
            {
            "storage_id": 1,
            "width": 110,
            "height": 222
            },
            {
            "storage_id": 2,
            "width": 110,
            "height": 222
            }
            //动态配图,都是图片ID
            //如果没有"storage": []
        ]
        //动态内容
    },
    "tool": {
        "feed_digg_count": 5,
        "feed_view_count": 10,
        "feed_comment_count": 2,
        "is_digg_feed": 0,
        "is_collection_feed": 0
        //动态工具栏使用，如果工具栏有增加，只会在这里增加
        //所有字段如果没有，则为0
    },
    "comments": [
        "comment_id": 11,
        "create_at": "2017-03-02 08:14:13",
        "comment_content": "啦啦啦",
        "user_id": 1,
        "reply_to_user_id": 0,
        "comment_mark": 0,
        //返回最新3条(如果有3条及以上)
        //如果为空comments: []
    ]
}
```

## 返回字段

| name     | type     | must     | description |
|----------|:--------:|:--------:|:--------:|
| user_id  | int      | yes      | 用户标识 |
| feed_mark   | int   | yes | 移动端标记 |
| feed	   | json	  | yes		 | 动态内容 |
| tool     | json  	  | yes 	 | 工具栏数据 |
| is_digg_feed   | int  | yes | 是否已点赞  0-未赞 1-已赞|
| is_collection_feed  | int | yes | 是否已收藏  0-未收藏 1-已收藏|
| comments | array    | yes      | 最新3条评论 |
| comment_id  | string | yes      | 评论id |
| create_at | string  | yes      | 评论时间 |
| comment_content     | string        | yes      | 评论内容 |
| user_id     | int   | yes    | 评论者id |
| to_user_id    | int | yes    | 动态作者id |
| reply_to_user_id | int | yes    | 被回复者id |
| comment_mark | int  | yes    | 评论的移动端标记 |

code请参见[Feed消息对照表](Feed消息对照表.md)