<?php
/**
 * Class PlaceForm.php , Last changed 22.09.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Forms;

use App\Entities\PlaceEntity;
use Nette;

class PlaceForm implements IBaseForm
{
    use Nette\SmartObject;

	/** @var BaseForm */
	public $form;

	/** @var null|PlaceEntity */
	private $defaultData = null;

	public function __construct(BaseForm $baseForm)
	{
		$this->form = $baseForm;
	}

	/**
	 * @param PlaceEntity $entity
	 */
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

		$form->addTextArea('description', 'Popis', null, 8)
			->setAttribute('placeholder', 'Popis a informace o místě')
			->setAttribute('class', 'form-control word-count');

		$form->addText('plusDesc', 'Plusy v místě')
			->setAttribute('class', 'form-control');

		$form->addText('minusDesc', 'Mínusy v místě')
			->setAttribute('class', 'form-control');

		$form->addMapPlacePicker('mapPlace', 'Označte místo na mapě', '100%', '500px')
			->setRequired($presenter->getTranslator()->translate('default.forms.messages.requiredMap'));

		$form->addUploadInput('photo', 'Nahrajte novou fotku');

		$form->addSubmit('send', 'default.buttons.save')
			->setAttribute('class', 'btn btn-default btn-success btn-sm');

		if (!$form->isSubmitted() && $this->defaultData)
		{
			$defaultData = $this->defaultData->toArray();
			$defaultData['mapPlace'] = implode(';', [$defaultData['placeLatitude'], $defaultData['placeLongitude'], $defaultData['placeZoom']]);
			$form->setDefaults($defaultData);
		}

		$form->onValidate[] = [$this, 'onValidate'];

		$form->onSuccess[] = [$this, 'onSuccess'];

		return $form;
	}

	public function onValidate(\App\Models\Form $form, $vars)
	{
		list($vars->placeLatitude, $vars->placeLongitude, $vars->placeZoom) = explode(';', $vars->mapPlace);

		if($this->defaultData && isset($this->defaultData->id)) {
			$existNearby = $form->getPresenter()->entityManager->getRepository(PlaceEntity::class)->findBy([
				'placeLatitude <= ' => $vars->placeLatitude + 0.00008, 'placeLatitude >= ' => $vars->placeLatitude - 0.00008,
				'placeLongitude <= ' => $vars->placeLongitude + 0.00008, 'placeLongitude >= ' => $vars->placeLongitude - 0.00008,
				'id !=' => $this->defaultData->id
			]);
		} else {
			$existNearby = $form->getPresenter()->entityManager->getRepository(PlaceEntity::class)->findBy([
				'placeLatitude <= ' => $vars->placeLatitude + 0.00008, 'placeLatitude >= ' => $vars->placeLatitude - 0.00008,
				'placeLongitude <= ' => $vars->placeLongitude + 0.00008, 'placeLongitude >= ' => $vars->placeLongitude - 0.00008,
			]);
		}

		if(count($existNearby) > 0) {
			foreach ($existNearby as $item) {
				$form->addError('Promiňte, ale v této lokaci již existuje spot. ' . $item->name );
			}
		}
	}

	public function onSuccess(\App\Models\Form $form, $vars)
	{
		if (!$this->defaultData) {
			$place = new \App\Entities\PlaceEntity();
		} else {
			$place = $this->defaultData;
			$place->changedOn = new Nette\Utils\DateTime();
		}

		/** @var Nette\Http\FileUpload $photo */
		$photo = $vars->photo;

		$vars->description = Nette\Utils\Strings::normalize($vars->description);

		list($vars->placeLatitude, $vars->placeLongitude, $vars->placeZoom) = explode(';', $vars->mapPlace);
		unset($vars->mapPlace);

		$place->setEntityValues($vars);

		$place->user = $form->getPresenter()->getUserEntity();

		if ($photo->isOk()) {
			if ($photo->isImage()) {
				$placePhoto = new \App\Entities\PhotoEntity();
				$placePhoto->filename = $photo->getSanitizedName();
				$placePhoto->filesize = $photo->getSize();
				$placePhoto->mimetype = $photo->getContentType();
				$placePhoto->filehash = md5($photo->getContents());

				$form->getPresenter()->entityManager->persist($placePhoto)->flush();

				$place->addPhoto($placePhoto);

				$photo->move($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $placePhoto->filehash);

				$thumb = Nette\Utils\Image::fromFile($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $placePhoto->filehash);
				$thumb->resize(400, 300, Nette\Utils\Image::EXACT);
				$thumb->save($form->getPresenter()->configRepository->photosThumbsPath . DIRECTORY_SEPARATOR . $placePhoto->filehash, 95, Nette\Utils\Image::JPEG);

			} else {
				$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.fileNotImage'), 'error');
			}
		}

		$form->getPresenter()->entityManager->persist($place)->flush();

		$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.itemSaved'));
		$form->getPresenter()->redirect(':edit', ['id' => $place->id]);
	}
}