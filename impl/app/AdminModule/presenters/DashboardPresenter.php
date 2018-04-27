<?php
/**
 * Created by PhpStorm.
 * User: tech
 * Date: 28.7.2016
 * Time: 9:14
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Messages\Messages;
use App\AdminModule\Components\Events\Events;
use App\AdminModule\Components\Wall\Wall;
use Nette;

/**
 * Class HomepagePresenter
 * @package App\AdminModule\Presenters
 * @author Lukáš Vlček
 */
class DashboardPresenter extends BasePresenter
{
	public function beforeRender()
	{
		parent::beforeRender();

		$this->template->totalPlaces = $this->entityManager->getRepository(\App\Entities\PlaceEntity::class)->countBy([]);

		$this->template->totalLastMonthPlaces = $this->entityManager->getRepository(\App\Entities\PlaceEntity::class)->countBy(['createdOn >=' => (new Nette\Utils\DateTime())->modify('- 1 month')]);

		$this->template->totalDrones = $this->entityManager->getRepository(\App\Entities\DroneEntity::class)->countBy([]);

		$this->template->totalLastMonthDrones = $this->entityManager->getRepository(\App\Entities\DroneEntity::class)->countBy(['createdOn >=' => (new Nette\Utils\DateTime())->modify('- 1 month')]);

		$this->template->totalUsers = $this->entityManager->getRepository(\App\Entities\UserEntity::class)->countBy([]);

		$this->template->totalLastMonthUsers = $this->entityManager->getRepository(\App\Entities\UserEntity::class)->countBy(['createdOn >=' => (new Nette\Utils\DateTime())->modify('- 1 month')]);

		$this->template->totalUsersOnline = $this->entityManager->getRepository(\App\Entities\UserEntity::class)->countBy(['visitedOn >=' => (new Nette\Utils\DateTime())->modify('- 1 minutes')]);

		$this->template->totalUsersTodayOnline = $this->entityManager->getRepository(\App\Entities\UserEntity::class)->countBy(['visitedOn >=' => (new Nette\Utils\DateTime())->modify('- 1 day')]);
	}

	public function createComponentMessages($name)
	{
		return new Messages($this, $name);
	}

	public function createComponentEvents($name)
	{
		return new Events($this, $name);
	}

	public function createComponentWall($name)
	{
		return new Wall($this, $name);
	}
}