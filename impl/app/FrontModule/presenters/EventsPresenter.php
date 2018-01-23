<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\FrontModule\Presenters;

use Nette;

class EventsPresenter extends BasePresenter
{
	public function renderDefault()
	{
		$this->template->events = $this->getPresenter()->entityManager->getRepository(\App\Entities\EventEntity::class)
			->findBy(['eventDate >=' => new Nette\Utils\DateTime(), 'isPrivate' => 0], ['eventDate' => 'ASC']);
	}
}