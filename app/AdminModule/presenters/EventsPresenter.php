<?php
/**
 * Class EventsPresenter.php , Last changed 29.1.17 0:33
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Presenters;

use App\Models\Grid;

class EventsPresenter extends BasePresenter
{
	/** @var \App\Managers\EventsManager @inject */
	public $eventsManager;

	/** @var \App\AdminModule\Forms\EventForm @inject */
	public $eventForm;

	private $event;

	public function actionEdit($id)
	{
		$this->event = $this->eventsManager->getEvent($id);

		if(!$this->event) {
			throw new \Exception($this->getTranslator()->translate('default.messages.itemNotFind'));
		}

		$this->eventForm->setDefaultData($this->event);

		$this->template->event = $this->event;
	}

	public function actionDisplay($id)
	{
		$this->event = $this->eventsManager->getEvent($id);

		if(!$this->event) {
			throw new \Exception($this->getTranslator()->translate('default.messages.itemNotFind'));
		}

		$this->template->event = $this->event;
	}

	public function createComponentEventForm($name)
	{
		return $this->eventForm->create($this, $name);
	}

	public function createComponentPublicEventsGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->eventsManager->getEvents(true));

		$grid->addColumnDateTime('eventDate', 'Začátek události')
			->setFitContent(true);

		$grid->addColumnText('name', 'Název')
			->getElementPrototype('th')->setAttribute('class', 'col-md-2');

		$grid->addColumnText('user.username', 'Pořadatel')
			->getElementPrototype('th')->setAttribute('class', 'col-md-2');

		$grid->addColumnText('user.email', 'Kontakt')
			->getElementPrototype('th')->setAttribute('class', 'col-md-2');

		$grid->addColumnText('eventType.name', 'Typ')
			->getElementPrototype('th')->setAttribute('class', 'col-md-2');

		$grid->addColumnText('maxUsers', 'Počet přihlášek')
			->setRenderer(function ($item) {
				return $item->maxUsers > 0 ? $item->maxUsers : 'neomezeně';
			})
			->getElementPrototype('th')->setAttribute('class', 'col-md-2');

		$grid->addAction('join', $this->getTranslator()->translate('administrator.events.buttons.join'), 'join!')
			->setClass('btn btn-blue btn-xs');

		$grid->addAction('logout', $this->getTranslator()->translate('administrator.events.buttons.logout'), 'logout!')
			->setClass('btn btn-danger btn-xs');

		$grid->addAction('display', $this->getTranslator()->translate('default.buttons.display'))
			->setClass('btn btn-success btn-xs');

		$grid->allowRowsAction('join', function ($item) {
			return $this->hasUserJoined($item->id) ? false : true;
		});
		$grid->allowRowsAction('logout', function ($item) {
			return $this->hasUserJoined($item->id) ? true : false;
		});

		return $grid;
	}

	public function createComponentUserEventsGrid($name)
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

		$grid->addAction('display', $this->getTranslator()->translate('default.buttons.display'))
			->setClass('btn btn-success btn-xs');

		$grid->addAction('edit', $this->getTranslator()->translate('default.buttons.edit'))
			->setClass('btn btn-blue btn-xs');

		return $grid;
	}

	public function handleJoin($id)
	{
		try {
			$this->eventsManager->joinUser($id, $this->userEntity);
			$this->flashMessage($this->getTranslator()->translate('default.messages.weJoined'));
		} catch (\Exception $exception) {
			$this->flashMessage($exception->getMessage(), 'error');
		}

		$this->redirect('this');
	}

	public function handleLogout($id)
	{
		try {
			$this->eventsManager->logoutUser($id, $this->userEntity);
			$this->flashMessage($this->getTranslator()->translate('default.messages.weLogout'));
		} catch (\Exception $exception) {
			$this->flashMessage($exception->getMessage(), 'error');
		}

		$this->redirect('this');
	}

	public function handleRemoveEventPhoto($photoId)
	{

	}

	private function hasUserJoined($eventId)
	{
		return $this->eventsManager->hasUserJoined($eventId, $this->userEntity);
	}
}