<?php
/**
 * Class RouterFactory.php , Last changed 28.10.16 23:37
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;

		$router[] = new Route('sitemap.xml', array('module' => 'Front', 'presenter' => 'Sitemap', 'action' => 'default'));

		$router[] = new Route('obchody/[<locale [a-z]{2}>/]', array('module' => 'Front', 'presenter' => 'Eshop', 'action' => 'default'));

		$router[] = new Route('u/<username>', array('module' => 'Front', 'presenter' => 'Pilots', 'action' => 'detail'));

		$router[] = new Route('u/<username>/machines', array('module' => 'Front', 'presenter' => 'Pilots', 'action' => 'machines'));

		$router[] = new Route('u/<username>/places', array('module' => 'Front', 'presenter' => 'Pilots', 'action' => 'places'));

		$router[] = new Route('administrator/[<locale [a-z]{2}>/]<presenter>/<action>[/<id>]', array('module' => 'Admin', 'presenter' => 'Dashboard', 'action' => 'default')/*, Route::SECURED*/);

		$router[] = new Route('[<locale [a-z]{2}>/]<presenter>/<action>[/<id>[-<slug>]]', array('module' => 'Front', 'presenter' => 'Places', 'action' => 'default'));

		return $router;
	}

}
