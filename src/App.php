<?php

namespace DI\Bridge\Slim;

use DI\ContainerBuilder;

/**
 * Slim application configured with PHP-DI.
 *
 * As you can see, this class is very basic and is only useful to get started quickly.
 * You can also very well *not* use it and build the container manually.
 */
class App extends \Slim\App
{
    public function __construct(array $settings = [])
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->addDefinitions(__DIR__ . '/config.php');
        $containerBuilder->addDefinitions($settings);
        $this->configureContainer($containerBuilder);
        $container = $containerBuilder->build();

        parent::__construct($container->get('settings'));
        $this->setContainer($container);
        $this->getRouter()->setDefaultInvocationStrategy($container->get('foundHandler'));
        $this->getRouter()->setCallableResolver($container->get('callableResolver'));
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
