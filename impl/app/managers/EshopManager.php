<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\Managers;
use App\Entities\UserEntity;
use App\Entities\UserProductEntity;
use Nette\Application\BadRequestException;

/**
 * Class EshopManager
 * @package App\Managers
 */
class EshopManager extends BaseManager
{
	/**
	 * @param $slug
	 * @return mixed|null|UserEntity
	 * @throws BadRequestException
	 */
	public function getBySlug($slug)
	{
		$shop = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['username' => $slug, 'shopEnabled' => true]);
		if(!$shop) {
			throw new BadRequestException('Tento obchod neexistuje.');
		}
		return $shop;
	}

	/**
	 * @param $id
	 * @return mixed|null|UserProductEntity
	 * @throws BadRequestException
	 */
	public function getUserProductById($id)
	{
		$product = $this->entityManager->getRepository(UserProductEntity::class)->findOneBy(['id' => $id]);
		if(!$product) {
			throw new BadRequestException('Tato položka neexistuje.');
		}
		return $product;
	}

	public function deleteUserProductPhoto($photo_id)
	{
		$photo = $this->entityManager->getRepository(\App\Entities\PhotoEntity::class)->find($photo_id);

		$moreFilehashExists = count($this->entityManager->getRepository(\App\Entities\PhotoEntity::class)->findBy(['filehash' => $photo->filehash])) > 1 ? true : false;

		if(!$moreFilehashExists) {
			\Nette\Utils\FileSystem::delete($this->configRepository->photosPath . DIRECTORY_SEPARATOR . $photo->filehash);
		}

		$this->entityManager->remove($photo)->flush();

		return true;
	}
}