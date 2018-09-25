<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\AdminModule\Forms;


use App\Entities\UserProductEntity;

class UserProductForm implements IBaseForm
{
	use \Nette\SmartObject;

	/** @var BaseForm */
	public $form;

	/** @var null|UserProductEntity */
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

		$form->addText('productTitle', 'Název produktu')
			->setAttribute('class', 'form-control')
			->setRequired();

		$form->addTextArea('productDesc', 'Popis produktu')
			->setAttribute('class', 'form-control summernote');

		$form->addText('productPrice', 'Cena za jednotku (Kč)')
			->setDefaultValue('0.0000')
			->setAttribute('class', 'form-control')
			->setRequired();

		$form->addText('productShipment', 'Cena poštovného (Kč)')
			->setDefaultValue('0.0000')
			->setAttribute('class', 'form-control');

		$form->addInteger('productStock', 'Na skladě')
			->setAttribute('class', 'form-control')
			->setDefaultValue(1)
			->setRequired();

		$form->addSelect('productState', 'Stav zboží', [
				\App\Entities\UserProductEntity::STATE_NEWEST => 'Nové',
				\App\Entities\UserProductEntity::STATE_USED => 'Použité',
			])
			->setAttribute('class', 'form-control')
			->setRequired();

		$form->addUploadInput('productImage', 'Nahrát obrázek');

		$form->addSubmit('send', 'Uložit produkt')
			->setAttribute('class', 'btn btn-success pull-right');

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
			$product = new \App\Entities\UserProductEntity();
		} else {
			$product = $this->defaultData;
		}

		$product->setEntityValues($vars);

		$product->user = $form->getPresenter()->userEntity;

		/** @var \Nette\Http\FileUpload $photo */
		$photo = $vars->productImage;
		unset($vars->productImage);

		if ($photo->isOk()) {
			if ($photo->isImage()) {
				$photoEntity = new \App\Entities\PhotoEntity();
				$photoEntity->filename = $photo->getSanitizedName();
				$photoEntity->filesize = $photo->getSize();
				$photoEntity->mimetype = $photo->getContentType();
				$photoEntity->filehash = md5($photo->getContents());

				$form->getPresenter()->entityManager->persist($photoEntity)->flush();

				$product->addPhoto($photoEntity);

				$photo->move($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $photoEntity->filehash);

				$thumb = \Nette\Utils\Image::fromFile($form->getPresenter()->configRepository->photosPath . DIRECTORY_SEPARATOR . $photoEntity->filehash);
				$thumb->resize(400, 300, \Nette\Utils\Image::EXACT);
				$thumb->save($form->getPresenter()->configRepository->photosThumbsPath . DIRECTORY_SEPARATOR . $photoEntity->filehash, 95, \Nette\Utils\Image::JPEG);

			} else {
				$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.fileNotImage'), 'error');
			}
		}

		$form->getPresenter()->entityManager->persist($product);

		$form->getPresenter()->entityManager->flush();

		$form->getPresenter()->flashMessage($form->getTranslator()->translate('default.messages.itemSaved'));
		$form->getPresenter()->redirect(':editProduct', ['id' => $product->id]);
	}
}