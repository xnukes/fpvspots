<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\AdminModule\Presenters;

use App\Models\Grid;

class BuddiesPresenter extends BasePresenter
{
	protected function beforeRender()
	{
		parent::beforeRender();
	}

	public function createComponentMyBuddiesGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->userEntity->buddies);

		$grid->addColumnText('username', 'buddy.username', 'buddy.username');
	}
}