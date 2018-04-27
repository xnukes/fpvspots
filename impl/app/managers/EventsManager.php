<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\Managers;


use App\Entities\UserEntity;

class EventsManager extends BaseManager
{
	/**
	 * @param UserEntity $userEntity
	 * @return array
	 */
	public function getUserEvents(UserEntity $userEntity)
	{
		return $this->entityManager->getRepository(\App\Entities\EventEntity::class)->findBy(['user' => $userEntity]);
	}

	/**
	 * @param bool $public_only
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	public function getEvents($public_only = false)
	{
		if($public_only) {
			return $this->entityManager->getRepository(\App\Entities\EventEntity::class)->findBy(['isPrivate' => 0], ['eventDate' => 'ASC']);
		} else {
			return $this->entityManager->getRepository(\App\Entities\EventEntity::class);
		}
	}

	/**
	 * @param $id
	 * @return null|\App\Entities\EventEntity
	 */
	public function getEvent($id)
	{
		return $this->entityManager->getRepository(\App\Entities\EventEntity::class)->find($id);
	}

	/**
	 * @return array
	 */
	public function getEventTypes()
	{
		return $this->entityManager->getRepository(\App\Entities\EventTypeEntity::class)->findPairs([], 'name', [], 'id');
	}

	public function joinUser($eventId, UserEntity $userEntity)
	{
		$event = $this->getEvent($eventId);
		if(!$event) {
			throw new \Exception('Event not find.');
		}

		$event->getUsers()->add($userEntity);

		return $this->entityManager->persist($event)->flush();
	}

	public function logoutUser($eventId, UserEntity $userEntity)
	{
		$event = $this->getEvent($eventId);
		if(!$event) {
			throw new \Exception('Event not find.');
		}

		$event->getUsers()->remove($userEntity);

		return $this->entityManager->persist($event)->flush();
	}

	public function hasUserJoined($eventId, UserEntity $userEntity)
	{
		return false;
		$event = $this->getEvent($eventId);
		if(!$event) {
			throw new \Exception('Event not find.');
		}

		$exists = $event->getUsers()->contains($userEntity);
		return $exists;
	}
}