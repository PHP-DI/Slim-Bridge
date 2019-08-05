<?php

namespace DI\Bridge\Slim;

use DI\ContainerBuilder;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;
use \Invoker\CallableResolver as InvokerCallableResolver;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;

/**
 * Slim application configured with PHP-DI.
 *
 * As you can see, this class is very basic and is only useful to get started quickly.
 */
class App extends \Slim\App
{
    public function __construct(
        ?ContainerInterface $container = null,
        ?ResponseFactoryInterface $responseFactory = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null
    ) {
        if ($responseFactory == null) {
            $responseFactory = AppFactory::determineResponseFactory();
        }
        if (!$container) {
            $containerBuilder = new ContainerBuilder();
            $container = $containerBuilder->build();
        }
        parent::__construct($responseFactory, $container, $callableResolver, $routeCollector, $routeResolver);

        // Set resolvers
        $callableResolver = new InvokerCallableResolver($container);
        AppFactory::setCallableResolver(new CallableResolver($callableResolver));

        $resolvers = [
            // Inject parameters by name first
            new AssociativeArrayResolver(),
            // Then inject services by type-hints for those that weren't resolved
            new TypeHintContainerResolver($container),
            // Then fall back on parameters default values for optional route parameters
            new DefaultValueResolver(),
        ];

        $invoker = new Invoker(new ResolverChain($resolvers), $container);
        $this->getRouteCollector()->setDefaultInvocationStrategy(new ControllerInvoker($invoker));
    }
}
