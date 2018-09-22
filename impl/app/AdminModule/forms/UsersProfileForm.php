<?php
/**
 * This file is part of the project FPVSpots.info.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Forms;

use App\Entities\UserEntity;
use Nette;
use App\Models\Form;
use Nette\Http\FileUpload;
use Nette\SmartObject;

/**
 * Class UsersProfileForm
 * @package App\AdminModule\Forms
 * @author Lukáš Vlček
 */
class UsersProfileForm implements IBaseForm
{
    use Nette\SmartObject;

	/** @var BaseForm */
	public $form;

	/** @var \App\Managers\PhotoManager */
	public $photoManager;

	/** @var null|UserEntity */
	private $defaultData = null;

	public function __construct(BaseForm $baseForm, \App\Managers\PhotoManager $photoManager)
	{
		$this->form = $baseForm;
		$this->photoManager = $photoManager;
	}

    public function setDefaultData($entity)
    {
		$this->defaultData = $entity;
    }

    public function create(\App\AdminModule\Presenters\BasePresenter $presenter, $name)
	{
		$form = $this->form->create($presenter, $name);

		$form->addText('username', 'Uživatelské jméno')
			->setAttribute('alt','Uživatelské jméno nelze změnit.')
			->setAttribute('readonly', 'readonly')
			->setAttribute('class', 'form-control')
			->setRequired('Prosím vyplňte uživatelské jméno.');

        $form->addPassword('password', 'Vaše heslo')
			->setAttribute('class', 'form-control');

		$form->addPassword('passwordVerify', 'Heslo pro kontrolu')
			->setRequired(false)
			->setAttribute('class', 'form-control')
			->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password'])
			->addCondition(Form::FILLED, $form['password'])
				->setRequired('Prosím vypňte potvrzovací heslo.');

        $form->addText('firstName', 'Jméno')
			->setAttribute('class', 'form-control');

        $form->addText('lastName', 'Přijmení')
			->setAttribute('class', 'form-control');

        $form->addText('email', 'Zadejte e-mail')
			->setAttribute('class', 'form-control')
			->setRequired('Zadejte prosím váš e-mail')
			->addRule(Form::EMAIL, 'Zadejtee prosím platný e-mail.');

        $form->addText('phone', 'Telefon')
			->setAttribute('class', 'form-control');

        $form->addCheckbox('public', 'Veřejný profil')
			->setDefaultValue(0);

        $form->addUploadInput('photo', 'Profilová fotka');

		$form->addSubmit('send', 'Uložit profil');

		if (!$form->isSubmitted() && $this->defaultData)
		{
			$form->setDefaults($this->defaultData->toArray());
		}

		$form->onSuccess[] = [$this, 'onSuccess'];

		return $form;
	}

	public function onSuccess(\App\Models\Form $form, $vars)
	{
		/** @var FileUpload $photo */
		$photo = $vars->photo;

		unset($vars->photo);
		unset($vars->username); // cant change username

		if(empty($vars->password) && empty($vars->passwordVerify)) {
			unset($vars->password);
			unset($vars->passwordVerify);
		} else {
			$vars->password = Nette\Security\Passwords::hash($vars->password);
			unset($vars->passwordVerify);
		}

		$userEntity = $form->getPresenter()->userEntity;

		if($photo->isOk() && $photo->isImage()) {

			if(!empty($userEntity->photo)) {
				$this->photoManager->removePhoto($userEntity->photo->id);
			}

			$photoEntity = new \App\Entities\PhotoEntity();
			$photoEntity->filename = $photo->getSanitizedName();
			$photoEntity->filesize = $photo->getSize();
			$photoEntity->mimetype = $photo->getContentType();
			$photoEntity->filehash = md5($photo->getContents());

			$form->getPresenter()->entityManager->persist($photoEntity)->flush();

			$userEntity->photo = $photoEntity;

			$photo->move($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $photoEntity->filehash);

			$thumb = Nette\Utils\Image::fromFile($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $photoEntity->filehash);
			$thumb->resize(400, 300, Nette\Utils\Image::EXACT);
			$thumb->save($form->getPresenter()->configRepository->photosThumbsPath . DIRECTORY_SEPARATOR . $photoEntity->filehash, 95, Nette\Utils\Image::JPEG);

		}

		$userEntity->setEntityValues($vars);

		$form->getPresenter()->entityManager->persist($userEntity)->flush();

		$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.profileSaved'));
		$form->getPresenter()->redirect('this');
	}
}