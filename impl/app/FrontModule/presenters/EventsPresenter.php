<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\FrontModule\Presenters;

use App\Entities\EventEntity;
use App\Managers\EventsManager;
use Nette;

class EventsPresenter extends BasePresenter
{
	/** @var EventsManager @inject */
	public $eventsManager;

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
		$this->template->joinedCount = $this->event->getJoinedCount();
	}

	public function renderDefault()
	{
		$this->template->events = $this->getPresenter()->entityManager->getRepository(\App\Entities\EventEntity::class)
			->findBy(['eventDate >=' => new Nette\Utils\DateTime(), 'isPrivate' => 0], ['eventDate' => 'ASC']);
	}

	public function handleCancelJoin()
	{
		$cantLogout = false;
		if($this->event->hasOwner($this->userEntity)) {
			$this->flashMessage('Nemůžete se odhlásit z události, protože jste vlastníkem.', 'danger');
			$cantLogout = true;
		}
		if($this->event->hasStaff($this->userEntity)) {
			$this->flashMessage('Nemůžete se odhlásit z události, protože jste pořadatelem.', 'danger');
			$cantLogout = true;
		}
		if(!$cantLogout) {
			try {
				$this->eventsManager->logoutUser($this->event->id, $this->userEntity);
				$this->flashMessage('Byl jste odhlášen z události.');
			} catch (\Exception $exception) {
				$this->flashMessage($exception->getMessage(), 'danger');
			}
		}
		$this->redirect('this');
	}

	public function handleSendJoin()
	{
		try {
			$joined = false;
			if($this->event->getMaxUsers() > 0) {
				if($this->event->getJoinedCount() < $this->event->getMaxUsers()) {
					$joined = $this->eventsManager->joinUser($this->event, $this->userEntity);
				} else {
					$this->flashMessage('Počet přihlášek byl překročen. Nemůžete zaslat přihlášku.', 'danger');
				}
			} else {
				$joined = $this->eventsManager->joinUser($this->event, $this->userEntity);
			}
			if($joined) {
				$this->flashMessage('Zaslali jste přihlášku k události. Prosím vyčkejte na potvrzení pořadatelem.');
			}
		} catch (\Exception $e) {
			$this->flashMessage($e->getMessage(), 'danger');
		}
		$this->redirect('this');
	}
}