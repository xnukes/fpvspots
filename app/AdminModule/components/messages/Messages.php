<?php
/**
 * Class MessagesPresenter.phpenter.php , Last changed 21.1.17 2:23
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Components\Messages;

use App\AdminModule\Components\BaseComponent;
use Nette\ComponentModel\IContainer;

class Messages extends BaseComponent
{
	public function __construct(IContainer $parent, $name)
	{
		parent::__construct($parent, $name);
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'Messages.latte');

		$this->template->render();
	}
}