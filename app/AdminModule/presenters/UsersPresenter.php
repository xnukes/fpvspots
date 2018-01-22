<?php
/**
 * Class UsersPresenter.php , Last changed 18.11.16 23:37
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\UsersEditForm;
use App\AdminModule\Forms\UsersProfileForm;
use App\AdminModule\Forms\UsersProfilePageForm;
use App\Models\Grid;
use Nette;

/**
 * Class UsersPresenter
 * @package App\AdminModule\Presenters
 * @author Lukáš Vlček
 */
class UsersPresenter extends BasePresenter
{
	/** @var UsersEditForm @inject */
	public $usersEditForm;

	/** @var UsersProfileForm @inject */
	public $usersProfileForm;

	/** @var UsersProfilePageForm @inject */
	public $usersProfilePageForm;

	public function actionProfile()
	{
		$this->usersProfileForm->setDefaultData($this->userEntity);
		$this->usersProfilePageForm->setDefaultData($this->userEntity);
	}

	public function createComponentUsersEditForm($name)
	{
		return $this->usersEditForm->create($this, $name);
	}

	public function createComponentUsersProfileForm($name)
	{
		return $this->usersProfileForm->create($this, $name);
	}

	public function createComponentUsersProfilePageForm($name)
	{
		return $this->usersProfilePageForm->create($this, $name);
	}

	public function createComponentUsersGrid($name)
	{
		$source = $this->usersRepository->getTable('users');

		$grid = new Grid($this, $name);

		$grid->setDataSource($source);

		$grid->addColumnText('id', 'ID')
			->setSortable(true);

		$grid->addColumnLink('username', 'Uživ. jméno', 'edit')
			->setSortable(true);

		$grid->addColumnLink('email', 'E-mail', 'edit')
			->setSortable(true);

		$grid->addColumnText('role', 'Právo')
			->setSortable(true);

		$grid->addColumnDateTime('created_on', 'Vytvořeno')->setFormat('d.m.Y')
			->setSortable(true);

		$grid->addColumnDateTime('last_time', 'Naposledy')->setFormat('d.m.Y')
			->setSortable(true);

		$grid->addColumnStatus('enabled', 'Status')
			->addOption(1, 'Povoleno')
				->setClass('btn-success col-md-12')
				->endOption()
			->addOption(0, 'Zablokováno')
				->setClass('btn-danger col-md-12')
				->endOption()
			->onChange[] = [$this, 'handleBlock'];

		$grid->addAction('delete', 'Smazat', 'delete')
			->setIcon('close')
			->setClass('btn btn-xs btn-danger ajax');

		$grid->setDefaultSort(['last_time' => 'DESC']);

		return $grid;
	}

	public function handleBlock($id, $status)
	{
		$this->usersRepository->blockUser($id, $status);
		$this->flashMessage('Uživatel byl zablokován / odblokován.');
		$this->refreshSnippets();
	}

	public function refreshSnippets()
	{
		if($this->isAjax())
		{
			$this['usersGrid']->reload();
			$this->redrawControl('flashes');
		} else {
			$this->redirect('this');
		}
	}
}