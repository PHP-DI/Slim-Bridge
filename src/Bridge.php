<?php declare(strict_types=1);

namespace DI\Bridge\Slim;

use DI\Container;
use Invoker\CallableResolver as InvokerCallableResolver;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\CallableResolverInterface;

/**
 * This factory creates a Slim application correctly configured with PHP-DI.
 *
 * To use this, replace `Slim\Factory\AppFactory::create()`
 * with `DI\Bridge\Slim\Bridge::create()`.
 */
class Bridge
{
    public static function create(
        ?ContainerInterface $container = null,
        bool $prioritiseAttributesOverParams = false
    ): App {
        $container = $container ?: new Container;

        $callableResolver = new InvokerCallableResolver($container);

        $container->set(CallableResolverInterface::class, new CallableResolver($callableResolver));
        $app = AppFactory::createFromContainer($container);

        $container->set(App::class, $app);

        $controllerInvoker = static::createControllerInvoker($container, $prioritiseAttributesOverParams);
        $app->getRouteCollector()->setDefaultInvocationStrategy($controllerInvoker);

        return $app;
    }

    /**
     * Create an invoker with the default resolvers.
     */
    protected static function createInvoker(ContainerInterface $container): Invoker
    {
        $resolvers = [
            // Inject parameters by name first
            new AssociativeArrayResolver,
            // Then inject services by type-hints for those that weren't resolved
            new TypeHintContainerResolver($container),
            // Then fall back on parameters default values for optional route parameters
            new DefaultValueResolver,
        ];

        return new Invoker(new ResolverChain($resolvers), $container);
    }

    /**
     * Create a controller invoker with the default resolvers.
     */
    protected static function createControllerInvoker(ContainerInterface $container, bool $prioritiseAttributesOverParams): ControllerInvoker
    {
        return new ControllerInvoker(self::createInvoker($container), $prioritiseAttributesOverParams);
    }
}
