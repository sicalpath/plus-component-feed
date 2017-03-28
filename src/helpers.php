<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed;
use function asset as plus_asset;
use function view as plus_view;
/**
 * Generate an asset path for the application.
 *
 * @param string $path
 * @param bool $secure
 * @return string
 * @author Seven Du <shiweidu@outlook.com>
 * @homepage http://medz.cn
 */
function asset($path, $secure = null)
{
    $path = asset_path($path);
    return plus_asset($path, $secure);
}
/**
 * Get The component resource asset path.
 *
 * @param string $path
 * @return string
 * @author Seven Du <shiweidu@outlook.com>
 * @homepage http://medz.cn
 */
function asset_path($path)
{
    return component_name().'/'.$path;
}
/**
 * Get the component base path.
 *
 * @param string $path
 * @return string
 * @author Seven Du <shiweidu@outlook.com>
 * @homepage http://medz.cn
 */
function base_path($path = '')
{
    return dirname(__DIR__).$path;
}
/**
 * Get the component name.
 *
 * @return string
 * @author Seven Du <shiweidu@outlook.com>
 * @homepage http://medz.cn
 */
function component_name()
{
    return 'zhiyicx/plus-component-feed';
}
/**
 * Get the component route filename.
 *
 * @return string
 * @author Seven Du <shiweidu@outlook.com>
 * @homepage http://medz.cn
 */
function route_path()
{
    return base_path('/router.php');
}
/**
 * Get the component resource path.
 *
 * @return string
 * @author Seven Du <shiweidu@outlook.com>
 * @homepage http://medz.cn
 */
function resource_path()
{
    return base_path('/resource');
}
/**
 * Get the evaluated view contents for the given view.
 *
 * @param  string  $view
 * @param  array   $data
 * @param  array   $mergeData
 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
 *
 * @author Seven Du <shiweidu@outlook.com>
 * @homepage http://medz.cn
 */
function view($view = null, $data = [], $mergeData = [])
{
    $factory = plus_view();
    $factory->addLocation(base_path('/view'));
    if (func_num_args() === 0) {
        return $factory;
    }
    return $factory->make($view, $data, $mergeData);
}