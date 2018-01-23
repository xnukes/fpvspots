<?php
/**
 * Class Events.php , Last changed 30.05.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Components\Events;

use App\AdminModule\Components\BaseComponent;
use Nette;

class Events extends BaseComponent
{
    public function render()
    {
        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'Events.latte');

		$this->template->lastNextEvents = $this->getPresenter()->entityManager->getRepository(\App\Entities\EventEntity::class)
			->findBy(['eventDate >=' => new Nette\Utils\DateTime(), 'isPrivate' => 0], ['eventDate' => 'ASC']);

        $this->template->render();
    }
}