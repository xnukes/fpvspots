<?php
/**
 * Class EventsPresenter.php , Last changed 29.1.17 0:33
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Presenters;

use App\Entities\EventEntity;
use App\Entities\EventUserEntity;
use App\Helpers\SystemMailerHelper;
use App\Models\Grid;

class EventsPresenter extends BasePresenter
{
	/** @var \App\Managers\EventsManager @inject */
	public $eventsManager;

	/** @var \App\AdminModule\Forms\EventForm @inject */
	public $eventForm;

	/** @var EventEntity */
	private $event;

	/** @var SystemMailerHelper @inject */
	public $systemMailerHelper;

	public function actionEdit($id)
	{
		$this->event = $this->eventsManager->getEvent($id);

		if(!$this->event) {
			throw new \Exception($this->getTranslator()->translate('default.messages.itemNotFind'));
		}

		if(!$this->event->hasOwner($this->userEntity)) {
			$this->flashMessage('Tato událost není ve vaší správě.', 'danger');
			$this->redirect('default');
		}

		$this->eventForm->setDefaultData($this->event);

		$this->template->event = $this->event;
	}

	public function createComponentEventForm($name)
	{
		return $this->eventForm->create($this, $name);
	}

	public function createComponentMyEventsGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->eventsManager->getUserEvents($this->userEntity));

		$grid->addColumnDateTime('eventDate', 'Začátek události')
			->setFitContent(true);

		$grid->addColumnText('name', 'Název')
			->getElementPrototype('th')->setAttribute('class', 'col-md-4');

		$grid->addColumnText('user.email', 'Kontakt')
			->getElementPrototype('th')->setAttribute('class', 'col-md-2');

		$grid->addColumnText('eventType.name', 'Typ')
			->getElementPrototype('th')->setAttribute('class', 'col-md-2');

		$grid->addColumnText('maxUsers', 'Počet přihlášek')
			->setRenderer(function ($item) {
				return $item->maxUsers > 0 ? $item->maxUsers : 'neomezeně';
			})
			->getElementPrototype('th')->setAttribute('class', 'col-md-2');

		$grid->addAction('removeEvent!', $this->getTranslator()->translate('default.buttons.remove'))
			->setClass('btn btn-danger btn-xs')
			->setConfirm('Opravdu chcete odstranit událost %s', 'name');

		$grid->addAction('edit', $this->getTranslator()->translate('default.buttons.edit'))
			->setClass('btn btn-blue btn-xs');

		return $grid;
	}

	public function createComponentEventUsersGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setPrimaryKey('id');

		$grid->setDataSource($this->event->users);

		$grid->addColumnText('user.username','Pilot');

		$grid->addColumnText('user.email','E-mail');

		$grid->addColumnDateTime('created','Vytvořeno');

		$grid->addColumnStatus('state', 'Status')
			->addOption(EventUserEntity::STATE_WAIT, 'Čekající')->setIcon('check')->setClass('btn-info fullwidth')->endOption()
			->addOption(EventUserEntity::STATE_JOIN, 'Potvrzen')->setIcon('check')->setClass('btn-success fullwidth')->endOption()
			->addOption(EventUserEntity::STATE_STAFF, 'Pořadatel')->setIcon('check')->setClass('btn-danger fullwidth')->endOption()
			->onChange[] = [$this, 'eventUserStateChange'];
	}

	public function eventUserStateChange($identificator, $state)
	{
		/** @var EventUserEntity $eventUser */
		$eventUser = $this->entityManager->getRepository(EventUserEntity::class)->find($identificator);

		if($eventUser) {
			$eventUser->state = $state;
			if($state == EventUserEntity::STATE_JOIN) {
				$this->systemMailerHelper->sendMailEventAcceptJoin($eventUser->event, $eventUser->user);
			}
			$this->entityManager->persist($eventUser)->flush();
		}

		$this['eventUsersGrid']->reload();
	}

	public function handleRemoveEvent($id)
	{
		$event = $this->eventsManager->getEvent($id);

		if($event->user != $this->userEntity) {
			$this->flashMessage('Tato událost nepatří vám.', 'danger');
			$this->redirect('this');
		}

		$this->entityManager->remove($event);

		$this->entityManager->flush();

		$this->flashMessage('Událost byla odstraněna.');
		$this->redirect('this');
	}

	public function handleRemoveEventPhoto($photoId)
	{

	}
}