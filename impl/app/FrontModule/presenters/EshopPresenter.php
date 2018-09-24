<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\FrontModule\Presenters;


use App\Entities\UserEntity;

class EshopPresenter extends BasePresenter
{
	public $shops;

	public function actionDefault()
	{
		$this->shops = $this->entityManager->getRepository(UserEntity::class)->findBy(['shopEnabled' => true]);

		$this->template->shops = $this->shops;
	}

	public function actionDetail($slug)
	{

	}
}