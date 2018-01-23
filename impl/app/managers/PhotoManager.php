<?php
/**
 * Class PhotoManager.php , Last changed 10.06.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Managers;

use Nette;

class PhotoManager extends BaseManager
{
	public function removeDronePhoto($photo_id)
	{
		$photo = $this->entityManager->getRepository(\App\Entities\PhotoEntity::class)->find($photo_id);

		$moreFilehashExists = count($this->entityManager->getRepository(\App\Entities\PhotoEntity::class)->findBy(['filehash' => $photo->filehash])) > 1 ? true : false;

		if(!$moreFilehashExists) {
			\Nette\Utils\FileSystem::delete($this->configRepository->photosPath . DIRECTORY_SEPARATOR . $photo->filehash);
		}

		$this->entityManager->remove($photo)->flush();

		return true;
	}
	public function removePlacePhoto($photo_id)
	{
		$photo = $this->entityManager->getRepository(\App\Entities\PhotoEntity::class)->find($photo_id);

		$moreFilehashExists = count($this->entityManager->getRepository(\App\Entities\PhotoEntity::class)->findBy(['filehash' => $photo->filehash])) > 1 ? true : false;

		if(!$moreFilehashExists) {
			\Nette\Utils\FileSystem::delete($this->configRepository->photosPath . DIRECTORY_SEPARATOR . $photo->filehash);
		}

		$this->entityManager->remove($photo)->flush();

		return true;
	}

	/**
	 * Delete photo from database and if not exists others do delete file
	 * @param integer $photoId
	 * @return \Kdyby\Doctrine\EntityManager
	 */
	public function removePhoto($photoId)
	{
		$photo = $this->entityManager->getRepository(\App\Entities\PhotoEntity::class)->find($photoId);

		$moreFilehashExists = count($this->entityManager->getRepository(\App\Entities\PhotoEntity::class)->findBy(['filehash' => $photo->filehash])) > 1 ? true : false;

		if(!$moreFilehashExists) {
			\Nette\Utils\FileSystem::delete($this->configRepository->photosPath . DIRECTORY_SEPARATOR . $photo->filehash);
		}

		return $this->entityManager->remove($photo)->flush();
	}
}