<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\Managers;


use App\Entities\EventEntity;
use App\Entities\EventUserEntity;
use App\Entities\UserEntity;
use Nette\InvalidStateException;

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

	public function joinUser(EventEntity $event, UserEntity $userEntity)
	{
		if(!$event->isJoined($userEntity)) {
			$eventUserEntity = new EventUserEntity();
			$eventUserEntity->event = $event;
			$eventUserEntity->user = $userEntity;
			$eventUserEntity->state = EventUserEntity::STATE_WAIT;
			$this->entityManager->persist($eventUserEntity);
			$event->users[] = $eventUserEntity;
			return $this->entityManager->persist($event)->flush();
		} else {
			throw new InvalidStateException('Tento pilot je již přihlášen k události.');
		}
	}

	public function logoutUser($eventId, UserEntity $userEntity)
	{
		$event = $this->getEvent($eventId);
		if(!$event) {
			throw new \Exception('Event not find.');
		}

		$eventUserEntity = $event->removeFromUsers($userEntity);

		$this->entityManager->remove($eventUserEntity);

		$this->entityManager->flush();

		return $event;
	}

	public function hasUserJoined($eventId, UserEntity $userEntity)
	{
		$event = $this->getEvent($eventId);
		if(!$event) {
			throw new \Exception('Event not find.');
		}

		$exists = $event->getUsers()->contains($userEntity);
		return $exists;
	}
}