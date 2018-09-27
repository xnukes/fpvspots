<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\AdminModule\Presenters;


use App\AdminModule\Forms\UserProductForm;
use App\Entities\UserEntity;
use App\Managers\EshopManager;
use App\Models\Form;
use App\Models\Grid;
use Nette\Http\FileUpload;

class EshopPresenter extends BasePresenter
{
	/** @var UserProductForm @inject */
	public $userProductForm;

	/** @var EshopManager @inject */
	public $eshopManager;

	public $product;

	public function actionEditProduct($id)
	{
		$this->product = $this->eshopManager->getUserProductById($id);

		if(!$this->product) {
			throw new \Exception($this->getTranslator()->translate('default.messages.itemNotFind'));
		}

		$this->userProductForm->setDefaultData($this->product);

		$this->template->product = $this->product;
	}

	public function createComponentUserProductForm($name)
	{
		return $this->userProductForm->create($this, $name);
	}

	public function createComponentEshopSettingsForm($name)
	{
		$form = new Form($this, $name);

		$form->setTranslator($this->getTranslator());

		$form->addCheckbox('shopEnabled', 'Obchod Povolen')
			->setDefaultValue(false);

		$form->addText('shopTitle', 'Název obchodu')
			->setAttribute('class', 'form-control');

		$form->addUploadInput('shopCoverBg', 'Úvodní fotka');

		$form->addCheckbox('removeCoverBg', 'Odstranit úvodní fotku');

		$form->addTextArea('shopDesc', 'Popis a informace o obchodování', null, 8)
			->setAttribute('placeholder', 'Popis a informace o obchodování')
			->setAttribute('class', 'summernote form-control word-count');

		$form->addSubmit('send', 'Uložit nastavení')
			->setAttribute('class', 'btn btn-sm btn-success pull-right')
			->onClick[] = [$this, 'eshopSettingFormSuccess'];

		$form->setDefaults($this->userEntity->toArray());

		return $form;
	}

	public function createComponentProductsGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setPrimaryKey('id');

		$grid->setDataSource($this->userEntity->shopProducts);

		$grid->addColumnText('productTitle','Název položky');

//		$grid->addColumnText('category','Kategorie');

		$grid->addColumnText('productPrice','Cena')
			->setFitContent();

		$grid->addColumnText('productShipment','Poštovné')
			->setFitContent();

		$grid->addColumnText('productStock','Na skladě')
			->setFitContent();

		$grid->addAction('editProduct', 'Změnit')
			->setIcon('edit')
			->setClass('btn btn-default');

		$grid->addAction('delete', 'Smazat', 'deleteProduct!')
			->setIcon('remove')
			->setClass('btn btn-danger ajax')
			->setConfirm('Opravdu chcete smazat produkt %s ?', 'productTitle');
	}

	public function eshopSettingFormSuccess($submitBtn, $vars)
	{
		$this->userEntity->shopEnabled = $vars->shopEnabled;
		$this->userEntity->shopTitle = $vars->shopTitle;
		$this->userEntity->shopDesc = $vars->shopDesc;

		/** @var FileUpload $photo */
		$photo = $vars->shopCoverBg;
		if ($photo->isOk()) {
			if($photo->isImage()) {
				$photo->move($this->configRepository->coversPath . DIRECTORY_SEPARATOR . $this->userEntity->username);
				$this->userEntity->shopCoverBg = $this->userEntity->username;
			}
		}
		if(isset($vars->removeCoverBg) && $vars->removeCoverBg) {
			$this->userEntity->shopCoverBg = null;
		}

		$this->entityManager->persist($this->userEntity)->flush();

		$this->flashMessage('Vaše obchodní nastavení bylo uloženo.');
		$this->redirect('this');
	}

	public function handleDeleteProductPhoto($id)
	{
		$result = $this->eshopManager->deleteUserProductPhoto($id);

		if($result)
			$this->flashMessage($this->getTranslator()->translate('default.messages.fileIsDeleted'));

		$this->redirect('this');
	}

	public function handleDeleteProduct($id)
	{
		$product = $this->eshopManager->getUserProductById($id);
		if(!$product || $product->user != $this->userEntity) {
			$this->flashMessage('Tato akce není povolena.', 'danger');
			$this->redirect(':default');
		}
		$this->entityManager->remove($product);
		$this->entityManager->flush();
		$this->flashMessage('Produkt byl úspěšne odstraněn.');

		if($this->isAjax()) {
			$this['productsGrid']->reload();
			$this->redrawControl('flashes');
		} else {
			$this->redirect(':default');
		}
	}
}