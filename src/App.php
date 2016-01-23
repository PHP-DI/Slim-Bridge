<?php

namespace DI\Bridge\Slim;

use DI\ContainerBuilder;

/**
 * Slim application configured with PHP-DI.
 */
class App extends \Slim\App
{
    public function __construct()
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->addDefinitions(__DIR__ . '/config.php');
        $container = $containerBuilder->build();

        parent::__construct($container);
    }

    /**
     * Override this method to configure the container builder.
     *
     * For example, to load additional configuration files:
     *
     *     protected function configureContainer(ContainerBuilder $builder)
     *     {
     *         $builder->addDefinitions(__DIR__ . 'my-config-file.php');
     *     }
     */
    protected function configureContainer(ContainerBuilder $builder)
    {
    }
}
