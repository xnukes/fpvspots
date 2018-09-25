<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\Managers;
use App\Entities\UserEntity;
use Nette\Application\BadRequestException;

/**
 * Class EshopManager
 * @package App\Managers
 */
class EshopManager extends BaseManager
{
	public function getBySlug($slug)
	{
		$shop = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['username' => $slug, 'shopEnabled' => true]);
		if(!$shop) {
			throw new BadRequestException('Tento obchod neexistuje.');
		}
		return $shop;
	}
}