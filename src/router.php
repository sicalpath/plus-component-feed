<?php

use Illuminate\Support\Facades\Route;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\base_path as component_base_path;

Route::middleware('web')
    ->namespace('Zhiyi\\Component\\ZhiyiPlus\\PlusComponentFeed\\Controllers')
    ->group(component_base_path('/routes/web.php'));
Route::prefix('/api/v1')
    ->middleware('api')
    ->namespace('Zhiyi\\Component\\ZhiyiPlus\\PlusComponentFeed\\Controllers')
    ->group(component_base_path('/routes/api.php'));

// Admin manage routes.
Route::namespace('Zhiyi\\Component\\ZhiyiPlus\\PlusComponentFeed\\AdminContaollers')
    ->prefix('/feed/admin')
    ->middleware('web')
    ->group(component_base_path('/routes/admin.php'));

Route::namespace('Zhiyi\\Component\\ZhiyiPlus\\PlusComponentFeed\\V2\\Controllers')
    ->prefix('/api/v2')
    ->middleware('api')
    ->group(component_base_path('/routes/api_v2.php'));
