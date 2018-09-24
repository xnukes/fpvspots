<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\AdminModule\Presenters;


use App\Entities\UserEntity;
use App\Models\Form;
use App\Models\Grid;

class EshopPresenter extends BasePresenter
{

	public function createComponentEshopSettingsForm($name)
	{
		$form = new Form($this, $name);

		$form->setTranslator($this->getTranslator());

		$form->addCheckbox('shopEnabled', 'Obchod Povolen')
			->setDefaultValue(false);

		$form->addText('shopTitle', 'Název obchodu')
			->setAttribute('class', 'form-control');

		$form->addUploadInput('shopCoverBg', 'Úvodní fotka');


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

		$grid->setDataSource([]);

		$grid->addColumnText('title','Název položky');

		$grid->addColumnText('category','Kategorie');

		$grid->addColumnText('product_price','Cena');

		$grid->addColumnDateTime('shipment_price','Poštovné');
	}

	public function eshopSettingFormSuccess($submitBtn, $vars)
	{
		$this->userEntity->shopEnabled = $vars->shopEnabled;
		$this->userEntity->shopTitle = $vars->shopTitle;
		$this->userEntity->shopDesc = $vars->shopDesc;

		$this->entityManager->persist($this->userEntity)->flush();

		$this->flashMessage('Vaše obchodní nastavení bylo uloženo.');
		$this->redirect('this');
	}
}