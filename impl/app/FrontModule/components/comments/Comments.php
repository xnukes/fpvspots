<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\FrontModule\Components\Comments;

use App\Entities\EventsCommentsEntity;
use App\FrontModule\Components\BaseComponent;
use App\FrontModule\Presenters\EventsPresenter;
use App\Models\Form;
use Nette\ComponentModel\IContainer;

class Comments extends BaseComponent
{
	private $comments;

	public function __construct(IContainer $parent, $name)
	{
		parent::__construct($parent, $name);

		$this->comments = $this->getComments();
	}

	public function getComments()
	{
		if($this->getPresenter() instanceof EventsPresenter) {
			return $this->getPresenter()
				->entityManager
				->getRepository($this->getCommentsEntityName())
				->findBy(['event' => $this->getPresenter()->event, 'parent' => null], ['created' => 'DESC']);
		} else {
			return null;
		}

	}

	private function getCommentsPrefix()
	{
		return strrev(explode(':', strrev($this->getPresenter()->getName()))[0]);
	}

	public function getCommentsEntityName()
	{
		return '\\App\\Entities\\' . $this->getCommentsPrefix() . 'CommentsEntity';
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'Comments.latte');

		$this->template->comments = $this->comments;

		$this->template->render();
	}

	public function createComponentCommentForm($name)
	{
		$form = new Form($this, $name);

		$form->addText('comment', 'Komentář')
			->setAttribute('class', 'form-control')
			->setRequired('Prosím zadejte komentář před odesláním.');

		$form->addSubmit('send', 'Odeslat')
			->setAttribute('class', 'btn btn-primary w-100 pointer');

		$form->onSuccess[] = [$this, 'onCommentFormSuccess'];

		return $form;
	}

	public function onCommentFormSuccess(Form $form, $vars)
	{
		if($this->getPresenter() instanceof EventsPresenter) {
			$entity = new EventsCommentsEntity();
			$entity->event = $this->getPresenter()->event;
			$entity->user = $this->getPresenter()->userEntity;
			$entity->text = $vars->comment;
			$this->flashMessage('Komentáč byl přidán.');
		} else {
			$form->addError('Tento typ komentáře není naprogramován.');
		}

		if(!$form->hasErrors()) {
			$this->getPresenter()->entityManager->persist($entity)->flush();
		}

		$this->redirect('this');
	}

	/**
	 * @param bool $throw
	 * @return \App\FrontModule\Presenters\BasePresenter|null
	 */
	public function getPresenter($throw = true)
	{
		return parent::getPresenter($throw);
	}
}