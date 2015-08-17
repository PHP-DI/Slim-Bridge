<?php

namespace DI\Bridge\Slim;

use DI\Container;
use DI\ContainerBuilder;
use Slim\App;

/**
 * Creates pre-configured classes to get started easily.
 */
class Quickstart
{
    /**
     * @return ContainerBuilder
     */
    public static function createContainerBuilder()
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->addDefinitions(__DIR__ . '/config.php');

        return $containerBuilder;
    }

    /**
     * @return Container
     */
    public static function createContainer()
    {
        return self::createContainerBuilder()->build();
    }

    /**
     * @return App
     */
    public static function createApplication()
    {
        return new App(self::createContainer());
    }
}
