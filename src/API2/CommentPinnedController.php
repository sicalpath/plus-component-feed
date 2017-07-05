<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;

class CommentPinnedController extends Controller
{
    public function index(Request $request)
    {
        dd($request->user());
    }
}
