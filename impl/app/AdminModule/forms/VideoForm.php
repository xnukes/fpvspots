<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\AdminModule\Forms;


use App\Entities\VideoEntity;
use Nette;

class VideoForm extends Nette\Object implements IBaseForm
{
	/** @var BaseForm */
	public $form;

	/** @var null|VideoEntity */
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

		$form->addText('name', 'Název')
			->setAttribute('class', 'form-control')
			->setRequired($presenter->getTranslator()->translate('default.forms.messages.required'));

		$form->addText('link', 'Odkaz')
			->setAttribute('class', 'form-control')
			->setRequired($presenter->getTranslator()->translate('default.forms.messages.required'));

		$form->addText('desc', 'Popis')
			->setAttribute('class', 'form-control');

		$form->addSelect('type', 'Typ videa', VideoEntity::TYPES);

		$form->addSubmit('send', 'default.buttons.save')
			->setAttribute('class', 'btn btn-default btn-success btn-sm');

		if (!$form->isSubmitted() && $this->defaultData)
		{
			$form->setDefaults($this->defaultData->toArray());
		}

		$form->onSuccess[] = [$this, 'onSuccess'];

		return $form;
	}

	public function onSuccess(\App\Models\Form $form, $vars)
	{
		if (!$this->defaultData) {
			$video = new \App\Entities\VideoEntity();
		} else {
			$video = $this->defaultData;
			$video->changedOn = new Nette\Utils\DateTime();
		}

		$video->setEntityValues($vars);
		$video->user = $form->getPresenter()->getUserEntity();

		$form->getPresenter()->entityManager->persist($video)->flush();
		$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.itemSaved'));
		$form->getPresenter()->redirect(':edit', ['id' => $video->id]);
	}
}