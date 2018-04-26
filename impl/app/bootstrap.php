<?php
const DS = DIRECTORY_SEPARATOR;
require __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(['194.228.32.103']); // ip or false = debug mode / true = production mode
$configurator->enableDebugger(__DIR__ . DS . '..' . DS . 'log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . DS . '..' . DS . 'temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . DS . 'config' . DS . 'config.neon');
$configurator->addConfig(__DIR__ . DS . 'config' . DS . 'config.local.neon');

$container = $configurator->createContainer();

return $container;
