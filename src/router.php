<?php
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\base_path as component_base_path;

Route::middleware('web')
    ->namespace('Zhiyi\\Component\\ZhiyiPlus\\PlusComponentFeed\\Controllers')
    ->group(component_base_path('/routes/web.php'));