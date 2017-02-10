<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed;

use Closure;
use Zhiyi\Component\Installer\PlusInstallPlugin\AbstractInstaller;
use function  Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\{
    asset_path,
    route_path,
    resource_path,
    base_path as component_base_path
};

class Installer extends AbstractInstaller
{


	public function getName(): string
	{
		return 'feed';
	}

	public function getVersion(): string
	{
		return '1.0.0';
	}

	public function getLogo(): string
	{
		return asset_path('/images/logo.png');
	}

	public function getAuthor(): array
	{
		return [
			'name' => '',
			'email' => '',
			'homepage' => ''
		];
	}

	/**
	 * register routers
	 * @return [type] [description]
	 */
	public function router()
	{
		return __DIR__.'/router.php';
	}

	/**
	 * component installer
	 * @param  Closure $next [description]
	 * @return [type]        [description]
	 */
	public function install(Closure $next)
	{

		$next();
	}

	/**
     * Do run update the compoent.
     *
     * @param Closure $next
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function update(Closure $next)
    {
        // include component_base_path('/src/table_column.php');
        $next();
    }

    /**
     * uninstall component
     * @param  Closure $next [description]
     * @return [type]        [description]
     */
    public function uninstall(Closure $next)
    {
        // Schema::dropIfExists('feeds');
        // Schema::dropIfExists('atmes');
        // Schema::dropIfExists('feed_diggs');
        // Schema::dropIfExists('feed_comments');
        $next();
    }

    /**
     * setting static files
     * @return [type] [description]
     */
    public function resource()
    {
        return dirname(__DIR__).'/resource';
    }

}