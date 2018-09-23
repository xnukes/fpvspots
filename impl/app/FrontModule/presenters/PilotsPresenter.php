<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\FrontModule\Presenters;

use App\Models\Grid;
use Nette\Utils\Html;

class PilotsPresenter extends BasePresenter
{
	public $pilots;

	/** @persistent */
	public $page = 0;

	public $perPage = 100;

	/** @var \App\Entities\UserEntity */
	public $pilot;

	/** @var array \App\Entities\PlaceEntity */
	public $places;

	public function actionDefault()
	{
		// TODO: please create component paginator for this
		$this->template->pilots = $this->entityManager->getRepository(\App\Entities\UserEntity::getClassName())
			->findBy(['public' => true], ['createdOn' => 'DESC'], $this->perPage, $this->page * $this->perPage);
	}

	public function actionDetail(string $username)
	{
		try {
			$this->template->pilot = $this->getPublicPilotByUsername($username);
		} catch (\Nette\Application\BadRequestException $exception) {
			$this->flashMessage($exception->getMessage(), 'error');
		}

		if($this->isAjax()) {
			$this->redrawControl('pilotContent');
			$this->redrawControl('footerCardButtons');
			$this->redrawControl('title');
		}
	}

	public function actionMachines(string $username)
	{
		$this->setView('detailMachines');

		try {
			$this->template->pilot = $this->getPublicPilotByUsername($username);
		} catch (\Nette\Application\BadRequestException $exception) {
			$this->flashMessage($exception->getMessage(), 'error');
		}

		if($this->isAjax()) {
			$this->redrawControl('pilotContent');
			$this->redrawControl('footerCardButtons');
			$this->redrawControl('title');
		}
	}

	public function actionPlaces($username)
	{
		$this->setView('detailSpots');

		$this->pilot = $this->getPublicPilotByUsername($username);

		$this->places = $this->pilot->places;

		$this->template->pilot = $this->pilot;

		if($this->isAjax()) {
			$this->redrawControl('pilotContent');
			$this->redrawControl('footerCardButtons');
			$this->redrawControl('title');
		}
	}

	public function createComponentMachinesGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->template->pilot->drones);

		$grid->addColumnLink('name', 'Název', 'Machines:detail')
			->setFilterText('name');

		$grid->addColumnText('ratings', 'Hodnocení')
			->setFitContent()
			->setRenderer(function ($machine) {
				$ratings = Html::el('div');
				$ratings->addHtml(Html::el('span', ['class' => 'badge badge-primary badge-pill my-1 float-right'])->setText($machine->getCalculatedRating()));
				$iconsBlock = Html::el('div', ['class' => 'd-inline-block mr-5 pr-4']);
				if($machine->getCalculatedRating() >= 1)
					$iconsBlock->addHtml(Html::el('i', ['class' => 'fa fa-star text-warning']));
				if($machine->getCalculatedRating() >= 2)
					$iconsBlock->addHtml(Html::el('i', ['class' => 'fa fa-star text-warning']));
				if($machine->getCalculatedRating() >= 3)
					$iconsBlock->addHtml(Html::el('i', ['class' => 'fa fa-star text-warning']));
				if($machine->getCalculatedRating() >= 4)
					$iconsBlock->addHtml(Html::el('i', ['class' => 'fa fa-star text-warning']));
				if($machine->getCalculatedRating() >= 4.9)
					$iconsBlock->addHtml(Html::el('i', ['class' => 'fa fa-star text-warning']));
				$ratings->addHtml($iconsBlock);
				return $ratings;
			});

		$grid->addColumnDateTime('createdOn', 'Vytvořeno')
			->setFitContent();

		$grid->setColumnReset(false);

		$grid->setOuterFilterRendering(true);

		$grid->setRememberState(false);

		return $grid;
	}

	public function createComponentSpotsGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->places);

		$grid->addColumnLink('name', 'Název', 'Places:detail');

		$grid->addColumnText('mapPlace', 'Google Mapy' )
			->setFitContent()
			->setRenderer(function ($place) {
				$target = str_replace(';', ',', $place->mapPlace);
				return Html::el('a', ['href' => 'https://www.google.cz/maps/@' . $target . 'z', 'target' => '_blank'])
					->addHtml(Html::el('i', ['class' => 'fa fa-google']))
					->addText(' Otevřít mapu');
			});

		$grid->addColumnDateTime('createdOn', 'Vytvořeno')
			->setFitContent();

		$grid->addFilterText('name', 'Název', ['name']);

		$grid->setColumnReset(false);
		$grid->setOuterFilterRendering(true);
		$grid->setRememberState(true);
		$grid->setRefreshUrl(false);

		return $grid;
	}

	/**
	 * @param string $username
	 * @return mixed|null|object
	 * @throws \Nette\Application\BadRequestException
	 */
	protected function getPublicPilotByUsername(string $username)
	{
		$pilot = $this->entityManager->getRepository(\App\Entities\UserEntity::getClassName())->findOneBy(['username' => $username, 'public' => true]);
		if(!$pilot) {
			$this->error('Pilot s tímto uživatelským jménem nebyl nalezen.', 404);
		}
		return $pilot;
	}
}