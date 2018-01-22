<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\AdminModule\Forms;

use App\Entities\DroneEntity;
use App\Entities\EventEntity;
use App\Managers\EventsManager;
use Kdyby\Events\Event;
use Nette;

class EventForm extends Nette\Object implements IBaseForm
{
	/** @var BaseForm */
	public $form;

	/** @var null|EventEntity */
	private $defaultData = null;

	/** @var  EventsManager */
	public $eventsManager;

	public function __construct(BaseForm $baseForm, EventsManager $eventsManager)
	{
		$this->form = $baseForm;
		$this->eventsManager = $eventsManager;
	}

	public function setDefaultData($entity)
	{
		$this->defaultData = $entity;
	}

	public function create(\App\AdminModule\Presenters\BasePresenter $presenter, $name)
	{
		$form = $this->form->create($presenter, $name);

		$form->addText('name', 'Jméno události:')
			->setAttribute('placeholder', 'Jméno události')
			->setAttribute('class', 'form-control')
			->setIcon('bookmark')
			->setFancyTooltip('Zadejte název události')
			->setRequired('Prosím zadejte události.');

		$form->addSelect('eventType', 'Typ události:', $this->eventsManager->getEventTypes())
			->setAttribute('placeholder', 'Typ události')
			->setAttribute('class', 'form-control')
			->setRequired('Prosím zadejte Typ události.');

		$form->addInteger('maxUsers', 'Počet přihlášek (0 = neomezeně):')
			->setAttribute('class', 'form-control')
			->setDefaultValue('0')
			->setRequired('Prosím zadejte počet přihlášek.');

		$form->addText('eventDate', 'Datum začátku:')
			->setAttribute('class', 'form-control datepicker')
			->setAttribute('data-format','dd.mm.yyyy')
			->setAttribute('data-lang','en')
			->setType('datepicker');

		$form->addCheckbox('isPrivate', 'Privátní událost (nebude zobrazena v seznamu)');

		$form->addUploadInput('photo', 'Nahrajte novou fotku');

		$form->addTextArea('description', 'Popis události:')
			->setAttribute('class', 'summernote')
			->setRequired('Prosím zadejte minimálně stručný popis události.');

		$form->addMapPlacePicker('mapPlace', 'Označte místo na mapě', '100%', '500px')
			->setRequired($presenter->getTranslator()->translate('default.forms.messages.requiredMap'));

		$form->addSubmit('send', 'default.buttons.save')
			->setAttribute('class', 'btn btn-default btn-success btn-sm');

		if (!$form->isSubmitted() && $this->defaultData)
		{
			$defaults = $this->defaultData->toArray();
			$defaults['eventType'] = $defaults['eventType']->id;
			$defaults['eventDate'] = $defaults['eventDate']->format('d.m.Y');
			$form->setDefaults($defaults);
		}

		$form->onSuccess[] = [$this, 'onSuccess'];

		return $form;
	}

	public function onSuccess(\App\Models\Form $form, $vars)
	{
		$vars->description = Nette\Utils\Strings::normalize($vars->description);

		if (!$this->defaultData) {
			$event = new \App\Entities\EventEntity();
		} else {
			$event = $this->defaultData;
			$event->changedOn = new Nette\Utils\DateTime();
		}

		$vars->eventType = $form->getPresenter()->entityManager->find(\App\Entities\EventTypeEntity::class, $vars->eventType);

		$event->setEntityValues($vars);

		$event->user = $form->getPresenter()->userEntity;

		/** @var Nette\Http\FileUpload $photo */
		$photo = $vars->photo;
		if ($photo->isOk()) {
			if ($photo->isImage()) {
				$photoEntity = new \App\Entities\PhotoEntity();
				$photoEntity->filename = $photo->getSanitizedName();
				$photoEntity->filesize = $photo->getSize();
				$photoEntity->mimetype = $photo->getContentType();
				$photoEntity->filehash = md5($photo->getContents());

				$form->getPresenter()->entityManager->persist($photoEntity)->flush();

				$event->getPhotos()->add($photoEntity);

				$photo->move($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $photoEntity->filehash);

				$thumb = Nette\Utils\Image::fromFile($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $photoEntity->filehash);
				$thumb->resize(400, 300, Nette\Utils\Image::EXACT);
				$thumb->save($form->getPresenter()->configRepository->photosThumbsPath . DIRECTORY_SEPARATOR . $photoEntity->filehash, 95, Nette\Utils\Image::JPEG);

			} else {
				$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.fileNotImage'), 'error');
			}
		}

		$form->getPresenter()->entityManager->persist($event)->flush();

		$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.itemSaved'));
		$form->getPresenter()->redirect(':edit', ['id' => $event->id]);
	}
}