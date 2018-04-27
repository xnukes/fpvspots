<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\Managers;

use Doctrine\Common\Collections\ArrayCollection;

class VideoManager extends BaseManager
{
	/**
	 * @param $id
	 * @return null|object|\App\Entities\VideoEntity
	 */
	public function getVideo($id)
	{
		return $this->entityManager->getRepository(\App\Entities\VideoEntity::getClassName())->find($id);
	}

	/**
	 * @param \App\Entities\UserEntity $user
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getUserVideos(\App\Entities\UserEntity $user)
	{
		return new ArrayCollection($user->videos);
	}

	/**
	 * @param \App\Entities\UserEntity $userEntity
	 * @param $videoId
	 * @return \Kdyby\Doctrine\EntityManager
	 * @throws \Exception
	 */
	public function removeUserVideo(\App\Entities\UserEntity $userEntity, $videoId)
	{
		$video = $this->entityManager->getRepository(\App\Entities\VideoEntity::getClassName())->findBy(['id' => $videoId, 'user' => $userEntity]);
		if(!$video) {
			throw new \Exception('Místo nebylo nalezeno !');
		}
		return $this->entityManager->remove($video)->flush();
	}
}