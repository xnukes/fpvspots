<?php
/**
 * This file is part of the project FPVSpots.info.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\FrontModule\Presenters;


use App\Models\Grid;
use Nette\Utils\Html;

class PilotsPresenter extends BasePresenter
{
	public $pilots;

	/** @persistent */
	public $page = 0;

	public $perPage = 10;

	public function actionDefault()
	{
		$this->template->pilots = $this->entityManager->getRepository(\App\Entities\UserEntity::getClassName())
			->findBy(['public' => true], ['createdOn' => 'DESC'], $this->perPage, $this->page * $this->perPage);
	}

	public function actionDetail(string $username)
	{
		$this->template->pilot = $this->getPublicPilotByUsername($username);

		if($this->isAjax()) {
			$this->redrawControl('pilotContent');
			$this->redrawControl('footerCardButtons');
			$this->redrawControl('title');
		}
	}

	public function actionMachines(string $username)
	{
		$this->setView('detailMachines');
		$this->template->pilot = $this->getPublicPilotByUsername($username);

		if($this->isAjax()) {
			$this->redrawControl('pilotContent');
			$this->redrawControl('footerCardButtons');
			$this->redrawControl('title');
		}
	}

	public function actionPlaces($username)
	{
		$this->setView('detailSpots');
		$this->template->pilot = $this->getPublicPilotByUsername($username);

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

		$grid->addColumnLink('name', 'Název', 'Machines:detail');

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

		$grid->setRememberState(false);

		return $grid;
	}

	public function createComponentSpotsGrid($name)
	{
		$grid = new Grid($this, $name);

		$grid->setDataSource($this->template->pilot->places);

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

		$grid->setColumnReset(false);

		$grid->setRememberState(false);

		return $grid;
	}

	protected function getPublicPilotByUsername(string $username)
	{
		$pilot = $this->entityManager->getRepository(\App\Entities\UserEntity::getClassName())->findOneBy(['username' => $username, 'public' => true]);
		if(!$pilot) {
			$this->error('Pilot s tímto uživatelským jménem nebyl nalezen.', 404);
		}
		return $pilot;
	}
}