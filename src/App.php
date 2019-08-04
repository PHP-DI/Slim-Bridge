<?php

namespace DI\Bridge\Slim;

use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use \Invoker\CallableResolver as InvokerCallableResolver;

/**
 * Slim application configured with PHP-DI.
 *
 * As you can see, this class is very basic and is only useful to get started quickly.
 * You can also very well *not* use it and build the container manually.
 */
class App
{
    public static function boot(ContainerInterface $container)
    {
        AppFactory::setContainer($container);
        $callableResolver = new InvokerCallableResolver($container);
        AppFactory::setCallableResolver(new CallableResolver($callableResolver));

        $app = AppFactory::create();

        $resolvers = [
            // Inject parameters by name first
            new AssociativeArrayResolver(),
            // Then inject services by type-hints for those that weren't resolved
            new TypeHintContainerResolver($container),
            // Then fall back on parameters default values for optional route parameters
            new DefaultValueResolver(),
        ];

        $invoker = new Invoker(new ResolverChain($resolvers), $container);
        $app->getRouteCollector()->setDefaultInvocationStrategy(new ControllerInvoker($invoker));

        return $app;
    }
}
