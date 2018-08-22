<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\FrontModule\Presenters;

use App\Entities\EventEntity;
use Nette;

class EventsPresenter extends BasePresenter
{
	/** @var EventEntity $event */
	public $event;

	public function actionDetail($id, $slug)
	{
		$this->event = $this->entityManager->getRepository(EventEntity::class)->find($id);

		if (!$this->event) {
			$this->flashMessage('Tato událost nebyla nalezena, zkuste to prosím později.', 'danger');
			$this->redirect('default');
		}

		$this->template->event = $this->event;
		$this->template->isJoined = $this->event->isJoined($this->userEntity);
		$this->template->joinedStatus = $this->event->getJoinedStatus($this->userEntity);
		$this->template->staffUsers = $this->event->getStaffUsers();
		$this->template->confirmedUsers = $this->event->getConfirmedUsers();
		$this->template->unconfirmedUsers = $this->event->getUnConfirmedUsers();
	}

	public function renderDefault()
	{
		$this->template->events = $this->getPresenter()->entityManager->getRepository(\App\Entities\EventEntity::class)
			->findBy(['eventDate >=' => new Nette\Utils\DateTime(), 'isPrivate' => 0], ['eventDate' => 'ASC']);
	}

	public function handleCancelJoin()
	{
		if($this->event->hasOwner($this->userEntity) || $this->event->hasStaff($this->userEntity)) {
			$this->flashMessage('Nemůžete se odhlásit z události, protože jste pořadatelem nebo vlastníkem.', 'danger');
			$this->redirect('this');
		}
	}

	public function handleSendJoin()
	{

	}
	// TODO: udelat i zvlast komentare ci diskuzi k eventu
}