<?php
/**
 * This file is part of the project FPVSpots.info.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Forms;

use App\Entities\UserEntity;
use Nette;

class UsersProfilePageForm extends Nette\Object implements IBaseForm
{
	/** @var BaseForm */
	public $form;

	/** @var null|UserEntity */
	private $defaultData = null;

	public function __construct(BaseForm $baseForm)
	{
		$this->form = $baseForm;
	}

	public function setDefaultData($entity)
	{
		$this->defaultData = $entity;
	}

	public function create(\App\AdminModule\Presenters\BasePresenter $presenter, $name)
	{
		$form = $this->form->create($presenter, $name);


		$form->addText('pageWebsite', 'WWW stránka')
			->setIcon('external-link')
			->setAttribute('class', 'form-control');

		$form->addText('pageFacebook', 'Facebook')
			->setIcon('facebook')
			->setAttribute('class', 'form-control');

		$form->addText('pageGoogleplus', 'Gooogle +')
			->setIcon('google-plus')
			->setAttribute('class', 'form-control');

		$form->addTextArea('pageContent' , 'HTML5 Obsah (podpora bootstrap 4.0 - http://getbootstrap.com/docs/4.0/)')
			->setAttribute('class', 'summernote form-control')
			->setAttribute('data-height', '300')
			->setAttribute('data-lang', 'cs-CZ');

		$form->addSubmit('send', 'Uložit stránku');

		if (!$form->isSubmitted() && $this->defaultData)
		{
			$form->setDefaults($this->defaultData->toArray());
		}

		$form->onSuccess[] = [$this, 'onSuccess'];

		return $form;
	}

	public function onSuccess(\App\Models\Form $form, $vars)
	{
		$userEntity = $form->getPresenter()->userEntity;

		$userEntity->setEntityValues($vars);

		$form->getPresenter()->entityManager->persist($userEntity)->flush();

		$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.profileSaved'));
		$form->getPresenter()->redirect('this');
	}
}