<?php

namespace App\Factory;

use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    /**
     * Get container.
     *
     * @return ContainerInterface The container
     * @throws Exception
     */
    public function createInstance(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();

        // Set up settings
        $containerBuilder->addDefinitions(__DIR__ . '/../../config/container.php');

        // Build PHP-DI Container instance
        return $containerBuilder->build();
    }
}
