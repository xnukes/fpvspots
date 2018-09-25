<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\FrontModule\Presenters;


use App\Entities\UserEntity;
use App\Managers\EshopManager;
use Nette\Application\BadRequestException;

class EshopPresenter extends BasePresenter
{
	public $shops;

	/** @var EshopManager @inject */
	public $eshopManager;

	public function actionDefault()
	{
		$this->shops = $this->entityManager->getRepository(UserEntity::class)->findBy(['shopEnabled' => true]);

		$this->template->shops = $this->shops;
	}

	public function actionDetail($slug)
	{
		try {
			$this->template->shop = $this->eshopManager->getBySlug($slug);
		} catch (BadRequestException $e) {
			$this->flashMessage($e->getMessage(), 'danger');
		}
	}

	public function actionProduct($slug, $pid)
	{
		try {
			$this->template->shop = $this->eshopManager->getBySlug($slug);
			$this->template->product = $this->eshopManager->getUserProductById($pid);
		} catch (BadRequestException $e) {
			$this->flashMessage($e->getMessage(), 'danger');
		}
	}
}