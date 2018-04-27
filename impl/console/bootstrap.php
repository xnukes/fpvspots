<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(false);
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->addDirectory(__DIR__.'/../app')
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.console.neon');
$configurator->addConfig(__DIR__ . '/../app/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
