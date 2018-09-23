<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\AdminModule\Presenters;


use App\Models\Form;
use App\Models\Grid;

class EshopPresenter extends BasePresenter
{
	public function createComponentEshopSettingsForm($name)
	{
		$form = new Form($this, $name);

		$form->setTranslator($this->getTranslator());

		$form->addCheckbox('shop_enabled', 'Obchod Povolen')
			->setDefaultValue(false);

		$form->addText('shop_title', 'Název obchodu')
			->setAttribute('class', 'form-control');

		$form->addUploadInput('shop_cover_bg', 'Úvodní fotka');


		$form->addTextArea('shop_desc', 'Popis a informace o obchodování', null, 8)
			->setAttribute('placeholder', 'Popis a informace o obchodování')
			->setAttribute('class', 'summernote form-control word-count');

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
}