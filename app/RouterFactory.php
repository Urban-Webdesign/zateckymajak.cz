<?php declare(strict_types = 1);

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;

class RouterFactory
{

	use Nette\StaticClass;

	public static function createRouter(): Nette\Routing\Router
	{
		$router = new RouteList();

		$router->withModule('Admin')
			->addRoute('admin/<presenter>/<action>[/<id>]', 'Homepage:default');

		$router->withModule('Front')
			->addRoute('[<lang=cs (cs)>/]', 'Homepage:default')
			->addRoute('[<lang=cs (cs)>/]rubriky', 'Homepage:sections')
			->addRoute('[<lang=cs (cs)>/]redakce', 'Homepage:authors')
			->addRoute('[<lang=cs (cs)>/]kontakt', 'Homepage:contact')
			->addRoute('[<lang=cs (cs)>/]mapa-stranek', 'Homepage:sitemap')
			->addRoute('[<lang=cs (cs)>/]dokumenty', 'Homepage:documents')
			->addRoute('[<lang=cs (cs)>/]<section>[/<page [0-9]>]', 'Section:default')
			->addRoute('[<lang=cs (cs)>/]<section>/<slug>', 'Section:article')
			->addRoute('[<lang=cs [a-z]{2}>/]<presenter>/<action>', 'Error:404');


		return $router;
	}

}
