<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\AdminModule\Presenters;

use App\Models\Grid;

class VideosPresenter extends BasePresenter
{
	public $videos;

	public function actionDefault()
	{
		$this->videos = [];
	}

	public function createComponentVideosGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->videos);

		$grid->addColumnText('name', 'Název', 'name')
			->setSortable();
//
//		$grid->addColumnText('latitude', 'Latitude')
//			->setFitContent()
//			->setRenderer(function ($item) {
//				return explode(';', $item->mapPlace)[0];
//			});
//
//		$grid->addColumnText('longtitude', 'Longtitude')
//			->setFitContent()
//			->setRenderer(function ($item) {
//				return explode(';', $item->mapPlace)[1];
//			});
//
//		$grid->addColumnDateTime('createdOn', 'Vytvořeno')
//			->setSortable()
//			->setFitContent();
//
//		$grid->addColumnDateTime('changedOn', 'Změněno')
//			->setSortable()
//			->setFitContent();
//
//		$grid->addAction('edit', $this->getTranslator()->translate('default.buttons.edit'))
//			->setIcon('edit');
//
//		$grid->addAction('remove', $this->getTranslator()->translate('default.buttons.remove'), 'remove!')
//			->setClass('btn btn-danger btn-xs ajax')
//			->setIcon('remove')
//			->setConfirm(function($item) {
//				return 'Opravdu chcete smazat položku ' . $item->name . '?';
//			});
//
//		$grid->addFilterText('name', 'Hledat název:');

		$grid->setOuterFilterRendering();
		$grid->setRememberState(false);

		return $grid;
	}
}