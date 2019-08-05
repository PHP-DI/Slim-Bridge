<?php declare(strict_types=1);

namespace DI\Bridge\Slim;

use DI\Container;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use \Invoker\CallableResolver as InvokerCallableResolver;

/**
 * This factory creates a Slim application correctly configured with PHP-DI.
 *
 * To use this, replace `Slim\Factory\AppFactory::create()`
 * with `DI\Bridge\Slim\Bridge::create()`.
 */
class Bridge
{
    public static function create(ContainerInterface $container = null): App
    {
        $container = $container ?: new Container;

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
