<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\FrontModule\Presenters;


use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Tracy\Debugger;

class PlacesPresenter extends BasePresenter
{
	public function beforeRender()
	{
		parent::beforeRender();

		//$this->template->places = $this->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->findAll();
	}

	public function actionDetail($id)
	{
		$place = $this->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->findOneBy(['id' => $id]);

		if (!$place) {
			$this->flashMessage('Tento spot nebyl v databázi nalezen.', 'danger');
			$this->redirect('default');
		}

		$this->template->place = $place;
	}

	public function handleGetGmapsMarkers()
	{
		$data = [];

		$qb = $this->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->createQueryBuilder('p');
		$qb->select('p,ph,u');
		$qb->join('p.user', 'u');
		$qb->leftJoin('p.photos', 'ph');

		$places = $qb->getQuery()->getArrayResult();

        $detailLink = $this->link('Places:detail');

		foreach ($places as $place) {
			$icon = '/Front/images/map-icon.png';
			if ($place['createdOn'] >= (new DateTime())->modify('- 1 month')) {
				$icon = '/Front/images/map-icon-new.png';
			}
			$data['places'][] = [
				'url' => $detailLink . '/' . $place['id'],
				'name' => $place['name'],
				'desc' => $place['description'],
				'plus' => $place['plusDesc'],
				'minus' => $place['minusDesc'],
				'place' => $this->getGmapsPointer($place['mapPlace']),
				'username' => $place['user']['username'],
				'icon' => $icon
			];
		}

		$this->sendJson($data);
	}
}