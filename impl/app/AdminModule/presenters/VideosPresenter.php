<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\AdminModule\Presenters;

use App\Managers\VideoManager;
use App\Models\Grid;

class VideosPresenter extends BasePresenter
{
	public $video;

	public $videos;

	/** @var \App\AdminModule\Forms\VideoForm @inject */
	public $videoForm;

	/** @var VideoManager @inject */
	public $videoManager;

	public function actionDefault()
	{
		$this->videos = $this->videoManager->getUserVideos($this->userEntity);
	}

	public function actionEdit($id)
	{
		$this->video = $this->videoManager->getVideo($id);

		if(!$this->video) {
			throw new \Exception($this->getTranslator()->translate('default.messages.itemNotFind'));
		}

		$this->videoForm->setDefaultData($this->video);

		$this->template->video = $this->video;
	}

	public function createComponentVideosGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->videos);

		$grid->addColumnText('name', 'Název', 'name')
			->setSortable();

		$grid->addColumnDateTime('createdOn', 'Vytvořeno')
			->setSortable()
			->setFitContent();

		$grid->addColumnDateTime('changedOn', 'Změněno')
			->setSortable()
			->setFitContent();

		$grid->addAction('edit', $this->getTranslator()->translate('default.buttons.edit'))
			->setIcon('edit');

		$grid->addAction('remove', $this->getTranslator()->translate('default.buttons.remove'), 'remove!')
			->setClass('btn btn-danger btn-xs ajax')
			->setIcon('remove')
			->setConfirm(function($item) {
				return 'Opravdu chcete smazat položku ' . $item->name . '?';
			});

		$grid->addFilterText('name', 'Hledat název:');

		$grid->setOuterFilterRendering();
		$grid->setRememberState(false);

		return $grid;
	}

	public function createComponentVideoForm($name)
	{
		return $this->videoForm->create($this, $name);
	}

	public function handleRemove($id)
	{
		try {
			$this->videoManager->removeUserVideo($this->userEntity, $id);
			$this->flashMessage('Video bylo smazáno.');
		} catch (\Exception $exception) {
			$this->flashMessage('Nastala chyba ! ' . $exception->getMessage(), 'error');
		}

		if($this->isAjax()) {
			$this['videosGrid']->reload();
			$this->redrawControl('flashes');
		} else {
			$this->redirect('this');
		}
	}
}