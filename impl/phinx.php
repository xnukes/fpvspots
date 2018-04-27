<?php

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/console/bootstrap.php';

$parameters = $container->getParameters();

return [
    'paths' => [
        'migrations' => __DIR__.'/../db/migrations',
        'seeds' => __DIR__.'/../db/seeds'
    ],
    'environments' => [
        "default_migration_table" => "_phinx_log",
        "default_database" => "default",
        "default" => array(
            "adapter" => $parameters['database']['driver'],
            "host" => $parameters['database']['host'],
            "name" => $parameters['database']['database'],
            "user" => $parameters['database']['user'],
            "pass" => $parameters['database']['password']
        )
    ]
];