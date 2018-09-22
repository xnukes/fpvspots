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
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Tracy\Debugger;

class PlacesPresenter extends BasePresenter
{
	/**
	 *
	 */
	public function beforeRender()
	{
		parent::beforeRender();

		//$this->template->places = $this->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->findAll();
	}

	/**
	 * @param $id
	 * @throws \Nette\Application\AbortException
	 */
	public function actionDetail($id)
	{
		$place = $this->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->findOneBy(['id' => $id]);

		if (!$place) {
			$this->flashMessage('Tento spot nebyl v databázi nalezen.', 'danger');
			$this->redirect('default');
		}

		$this->template->place = $place;
	}

	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function handleGetGmapsMarkers()
	{
		$data = [];

		$qb = $this->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->createQueryBuilder('places');
		$qb->select('places, photos, users');
		$qb->join('places.user', 'users');
		$qb->leftJoin('places.photos', 'photos');

		$qb->where('places.latitude > ?1 AND places.latitude < ?3 AND places.longitude > ?2 AND places.longitude < ?4');

		if($viewport = json_decode($this->getRequest()->getPost('viewport')))
		{
			$viewport = (array)$viewport;
			$qb->setParameter(1, $viewport[0]->latitude);
			$qb->setParameter(2, $viewport[0]->longitude);

			$qb->setParameter(3, $viewport[1]->latitude);
			$qb->setParameter(4, $viewport[1]->longitude);
		}

		$places = $qb->getQuery()->getArrayResult();

        $detailLink = $this->link('Places:detail');

		foreach ($places as $place) {
			$icon = '/Front/images/map-icon.png';
			if ($place['createdOn'] >= (new DateTime())->modify('- 1 month')) {
				$icon = '/Front/images/map-icon-new.png';
			}
			$data['places'][] = [
				'url'       => $detailLink . '/' . $place['id'],
				'name'      => $place['name'],
				'desc'      => $place['description'],
				'plus'      => $place['plusDesc'],
				'minus'     => $place['minusDesc'],
				'latitude'  => $place['latitude'],
				'longitude' => $place['longitude'],
				'zoom'      => $place['zoom'],
				'username'  => $place['user']['username'],
				'icon'      => $icon,
				'id'        => $place['id'],

			];
		}

		$this->sendJson($data);
	}
}