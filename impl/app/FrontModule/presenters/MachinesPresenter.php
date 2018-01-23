<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\FrontModule\Presenters;


use App\Entities\DroneEntity;
use Doctrine\ORM\AbstractQuery;
use Nette\Application\BadRequestException;
use Nette\Http\IResponse;

class MachinesPresenter extends BasePresenter
{
	/** @var DroneEntity */
	public $machine;

	/** @var */
	public $machines;

	/** @var int */
	public $page = 0;

	/** @var int */
	public $perPage = 500;

	/**
	 * @param $id
	 * @param $slug
	 * @throws BadRequestException
	 */
	public function actionDetail($id, $slug)
	{
		$this->template->machine = $this->machine = $this->entityManager->getRepository(\App\Entities\DroneEntity::getClassName())->find($id);
		if(!$this->machine) {
			$this->error('Machine does not exits !', IResponse::S404_NOT_FOUND);
		}
	}

	public function actionTop5()
	{
		$qb = $this->entityManager->getRepository(\App\Entities\DroneEntity::getClassName())->createQueryBuilder('d');
		$qb->select('d.id,d.name,u.username,u.public,p.filehash,AVG(r.rate) AS avgRate');
		$qb->leftJoin('d.ratings', 'r');
		$qb->leftJoin('d.photos', 'p');
		$qb->leftJoin('d.user', 'u');
		$qb->groupBy('d.id');
		$qb->orderBy('avgRate', 'DESC');
		$qb->setMaxResults(5);

		$this->machines = $qb->getQuery()->getResult();

//		dump($this->machines);
//		exit;
	}

	public function renderTop5()
	{
		$this->template->machines = $this->machines;
	}

	public function actionDefault()
	{
		$this->template->page = $this->page;
		$this->machines = $this->entityManager->getRepository(\App\Entities\DroneEntity::getClassName())
			->findBy([], ['createdOn' => 'DESC'], $this->perPage, $this->page * $this->perPage);
		$counter = $this->entityManager->getRepository(\App\Entities\DroneEntity::getClassName())->countBy([]);
		$this->template->existsmore = $counter > (count($this->machines) - ($this->page * $this->perPage)) ? true : false;
	}

	public function renderDefault()
	{
		$this->template->machines = $this->machines;
	}

	public function handlePage($page)
	{
		$this->template->page = $this->page = $page + 1;

		if ($this->isAjax()) {
			$this->redrawControl('machinesItems');
			$this->redrawControl('pageButton');
		} else {
			$this->redirect('this');
		}
	}

	public function handleRate($rate)
	{
		if(!$this->machine->hasIpRating()) {

			$rating = new \App\Entities\DroneRatingEntity();
			$rating->drone = $this->machine;
			$rating->ipAddress = $_SERVER['REMOTE_ADDR'];
			$rating->rate = $rate;

			$this->entityManager->persist($rating)->flush();

			$this->flashMessage('Děkujeme za váš hlas.' );
		} else {
			$this->flashMessage('Již jste hlasoval pro tento stroj.', 'error');
		}

		$this->entityManager->refresh($this->machine);

		if($this->isAjax()) {
			$this->redrawControl('ratings');
			$this->redrawControl('flashes');
		} else {
			$this->redirect('this');
		}
	}
}