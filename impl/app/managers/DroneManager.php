<?php
/**
 * Class DroneManager.php , Last changed 03.06.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Managers;


use Doctrine\Common\Collections\ArrayCollection;

class DroneManager extends BaseManager
{
    public function getUserDrones(\App\Entities\UserEntity $user)
    {
    	return new ArrayCollection($user->drones);
    }

	/**
	 * @param $id
	 * @return null|object|\App\Entities\DroneEntity
	 */
    public function getDrone($id)
    {
        return $this->entityManager->getRepository(\App\Entities\DroneEntity::getClassName())->find($id);
    }

    public function removeUserDrone(\App\Entities\UserEntity $userEntity, $id)
	{
		$drone = $this->entityManager->getRepository(\App\Entities\DroneEntity::getClassName())->findOneBy(['user' => $userEntity, 'id' => $id]);
		if(!$drone) {
			throw new \Exception('Stroj nebyl nalezen !');
		}
		return $this->entityManager->remove($drone)->flush();
	}
}