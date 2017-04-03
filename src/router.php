<?php

use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\base_path as component_base_path;

Route::middleware('web')
    ->namespace('Zhiyi\\Component\\ZhiyiPlus\\PlusComponentFeed\\Controllers')
    ->group(component_base_path('/routes/web.php'));
Route::prefix('/api/v1')
    ->namespace('Zhiyi\\Component\\ZhiyiPlus\\PlusComponentFeed\\Controllers')
    ->group(component_base_path('/routes/api.php'));

// Admin manage routes.
Route::namespace('Zhiyi\\Component\\ZhiyiPlus\\PlusComponentFeed\\AdminContaollers')
    ->prefix('/feed/admin')
    ->middleware('auth:web')
    ->group(component_base_path('/routes/admin.php'));
