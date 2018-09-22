<?php
/**
 * Class BaseManager.php , Last changed 20.1.17 23:49
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Managers;

use App\Models\ConfigRepository;
use Kdyby\Doctrine\EntityManager;
use Nette;

/**
 * Class BaseManager
 * @package App\Managers
 */
class BaseManager
{
    use Nette\SmartObject;

	/** @var EntityManager */
	public $entityManager;

	/** @var ConfigRepository */
	public $configRepository;

	public function __construct(EntityManager $entityManager, \Kdyby\Translation\Translator $translator, ConfigRepository $configRepository)
	{
		$this->entityManager		= $entityManager;
		$this->configRepository		= $configRepository;
	}
}