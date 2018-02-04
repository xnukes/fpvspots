<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

/** @var \Nette\DI\Container $container */
$container = require_once 'app/bootstrap.php';

/** @var \Kdyby\Doctrine\EntityManager $entityManager */
$entityManager = $container->getByType(\Kdyby\Doctrine\EntityManager::class);

return ConsoleRunner::createHelperSet($entityManager);