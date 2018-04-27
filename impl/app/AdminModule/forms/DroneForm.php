<?php
/**
 * Class DroneForm.php , Last changed 10.06.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Forms;

use App\Entities\DroneEntity;
use Nette;

class DroneForm extends Nette\Object implements IBaseForm
{
    /** @var BaseForm */
    public $form;

    /** @var null|DroneEntity */
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

        $form->addText('name', 'Jméno stroje:')
            ->setAttribute('placeholder', 'Jméno stroje')
            ->setAttribute('class', 'form-control')
            ->setIcon('odnoklassniki')
            ->setFancyTooltip('Zadejte název vašeho stroje')
            ->setRequired('Prosím zadejte jméno stroje.');

        $form->addSelect('type', 'Typ dronu')
            ->setAttribute('placeholder', 'Vyberte typ')
            ->setAttribute('class', 'form-control')
            ->setItems(\App\Entities\DroneEntity::TYPES)
            ->setRequired('Prosím vyberte typ stroje.');

        $form->addUploadInput('photo', 'Nahrajte novou fotku');

        $form->addTextArea('description', 'Popis', null, 8)
            ->setAttribute('placeholder', 'Popis stroje a jeho komponent')
            ->setAttribute('class', 'form-control word-count')
            ->setRequired(false);

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
        $vars->description = Nette\Utils\Strings::normalize($vars->description);

        if (!$this->defaultData) {
            $drone = new \App\Entities\DroneEntity();
        } else {
            $drone = $this->defaultData;
			$drone->changedOn = new Nette\Utils\DateTime();
        }

        /** @var Nette\Http\FileUpload $photo */
		$photo = $vars->photo;

		unset($vars->photo);

        $drone->setEntityValues($vars);

        $drone->user = $form->getPresenter()->getUserEntity();

		if ($photo->isOk()) {
			if ($photo->isImage()) {
				$dronePhoto = new \App\Entities\PhotoEntity();
				$dronePhoto->filename = $photo->getSanitizedName();
				$dronePhoto->filesize = $photo->getSize();
				$dronePhoto->mimetype = $photo->getContentType();
				$dronePhoto->filehash = md5($photo->getContents());

				$form->getPresenter()->entityManager->persist($dronePhoto)->flush();

				$drone->addPhoto($dronePhoto);

				$photo->move($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $dronePhoto->filehash);

				$thumb = Nette\Utils\Image::fromFile($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $dronePhoto->filehash);
				$thumb->resize(400, 300, Nette\Utils\Image::EXACT);
				$thumb->save($form->getPresenter()->configRepository->photosThumbsPath . DIRECTORY_SEPARATOR . $dronePhoto->filehash, 95, Nette\Utils\Image::JPEG);

			} else {
				$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.fileNotImage'), 'error');
			}
		}

		$form->getPresenter()->entityManager->persist($drone)->flush();


        $form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.itemSaved'));
        $form->getPresenter()->redirect(':edit', ['id' => $drone->id]);
    }
}