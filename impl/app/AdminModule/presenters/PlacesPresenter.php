<?php
/**
 * Class PlacesPreseter.php , Last changed 29.1.17 0:35
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Presenters;


use App\Models\Grid;
use Doctrine\Common\Collections\ArrayCollection;

class PlacesPresenter extends BasePresenter
{
	/** @var \App\Managers\PlaceManager @inject */
	public $placeManager;

	/** @var \App\Managers\PhotoManager @inject */
	public $photoManager;

	/** @var \App\AdminModule\Forms\PlaceForm @inject */
	public $placeForm;

	public $places;

	public $place;

	/**
	 *
	 */
	public function actionDefault()
	{
		$this->places = $this->placeManager->getUserPlaces($this->userEntity);
	}

	/**
	 * @param $id
	 * @throws \Exception
	 */
	public function actionEdit($id)
	{
		$this->place = $this->placeManager->getPlace($id);

		if(!$this->place) {
			throw new \Exception($this->getTranslator()->translate('default.messages.itemNotFind'));
		}

		$this->placeForm->setDefaultData($this->place);

		$this->template->place = $this->place;
	}

	/**
	 * @param $name
	 * @return Grid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentPlacesGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->places);

		$grid->addColumnText('name', 'Název', 'name')
			->setSortable();

		$grid->addColumnText('latitude', 'Latitude', 'latitude')
			->setFitContent();

		$grid->addColumnText('longitude', 'Longitude', 'longitude')
			->setFitContent();

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

	/**
	 * @param $name
	 * @return \App\Models\Form
	 */
	public function createComponentPlaceForm($name)
	{
		return $this->placeForm->create($this, $name);
	}

	/**
	 * @param $id
	 * @throws \Nette\Application\AbortException
	 */
	public function handleRemove($id)
	{
		try {
			$this->placeManager->removeUserPlace($this->userEntity, $id);
			$this->flashMessage('Místo bylo smazáno.');
		} catch (\Exception $exception) {
			$this->flashMessage('Nastala chyba ! ' . $exception->getMessage(), 'error');
		}

		if($this->isAjax()) {
			$this['placesGrid']->reload();
			$this->redrawControl('flashes');
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param $photo_id
	 * @throws \Nette\Application\AbortException
	 */
	public function handleRemovePlacePhoto($photo_id)
	{
		$result = $this->photoManager->removePlacePhoto($photo_id);

		if($result)
			$this->flashMessage($this->getTranslator()->translate('default.messages.fileIsDeleted'));

		$this->redirect('this');
	}
}