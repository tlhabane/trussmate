<?php

use DI\ContainerBuilder;

require_once(__DIR__ . '/../vendor/autoload.php');

$containerBuilder = new ContainerBuilder();
// Set up settings
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

try {
    // Build PHP-DI Container instance
    $container = $containerBuilder->build();
} catch (Exception $exception) {
    die($exception->getMessage());
}
