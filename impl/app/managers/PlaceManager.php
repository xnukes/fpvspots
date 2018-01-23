<?php
/**
 * Class PlaceManager.php , Last changed 22.09.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Managers;

use Doctrine\Common\Collections\ArrayCollection;

class PlaceManager extends BaseManager
{
	/**
	 * @param \App\Entities\UserEntity $user
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getUserPlaces(\App\Entities\UserEntity $user)
	{
		return new ArrayCollection($user->places);
	}

	/**
	 * @param $id
	 * @return null|object|\App\Entities\PlaceEntity
	 */
	public function getPlace($id)
	{
		return $this->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->find($id);
	}

	/**
	 * @param \App\Entities\UserEntity $userEntity
	 * @param $placeId
	 * @return \Kdyby\Doctrine\EntityManager
	 * @throws \Exception
	 */
	public function removeUserPlace(\App\Entities\UserEntity $userEntity, $placeId)
	{
		$place = $this->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->findBy(['id' => $placeId, 'user' => $userEntity]);
		if(!$place) {
			throw new \Exception('Místo nebylo nalezeno !');
		}
		return $this->entityManager->remove($place)->flush();
	}
}