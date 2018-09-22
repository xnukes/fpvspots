<?php
/**
 * Class BasePresenter.php , Last changed 18.1.17 22:32
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Presenters;

use App\Entities\UserEntity;
use App\Models\ConfigRepository;
use App\Models\SystemRepository;
use Kdyby\Doctrine\EntityManager;
use Nette,
	Nette\Application\UI\Presenter;
use Nette\Http;

/**
 * Class BasePresenter
 * @package App\Presenters
 * @author Lukáš Vlček
 */
class BasePresenter extends Presenter
{
	/** @var ConfigRepository @inject */
	public $configRepository;

	/** @var \Kdyby\Translation\Translator @inject */
	public $translator;

	/** @var EntityManager @inject */
	public $entityManager;

	/** @persist */
	public $locale;

	/** @var Nette\Security\IIdentity */
	private $identity;

	/** @var UserEntity */
	public $userEntity;

	protected function startup()
	{
		parent::startup();
		$this->identity = $this->getUser()->getStorage()->getIdentity();

		$this->template->userEntity = $this->getUserEntity();

		$this->userEntity = $this->getUserEntity();

		if($this->getUser()->isLoggedIn()) {
			$this->userEntity->visitedOn = new Nette\Utils\DateTime();
			$this->entityManager->persist($this->userEntity)->flush();
		}

		$this->template->addFilter('latitude', function ($mapPlace){
			return explode(';', $mapPlace)[0];
		});
		$this->template->addFilter('longitude', function ($mapPlace){
			return explode(';', $mapPlace)[1];
		});
	}

	public function getGmapsPointer($string)
	{
		return explode(';', $string);
	}

	protected function beforeRender()
	{
		$this->template->parameters = $this->configRepository;

		$this->template->systemVersion = SystemRepository::getSystemVersion($this->translator);

		$this->locale = 'cs';

		parent::beforeRender();
	}

	public function getTranslator()
    {
        return $this->translator;
    }

	public function getUserEntity()
	{
		if($this->getUser()->isLoggedIn()) {
			return $this->entityManager->getReference(UserEntity::class ,$this->getUser()->getId());
		}
		return false;
	}
}