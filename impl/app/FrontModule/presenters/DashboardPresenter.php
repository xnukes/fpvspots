<?php
/**
 * Class HomepagePresenter.php , Last changed 18.10.16 22:23
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\FrontModule\Presenters;

/**
 * Class HomepagePresenter
 * @package App\FrontModule\Presenters
 * @author Lukáš Vlček
 */
class DashboardPresenter extends BasePresenter
{
	public function actionDefault()
	{
		$this->template->machines = $this->entityManager->getRepository(\App\Entities\DroneEntity::getClassName())->findBy([], null, 5);
	}
}