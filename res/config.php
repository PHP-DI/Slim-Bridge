<?php

use DI\Bridge\Slim\CallableResolver;
use DI\Bridge\Slim\ControllerInvoker;
use DI\Container;
use DI\Scope;
use Interop\Container\ContainerInterface;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\ResolverChain;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use function DI\factory;
use function DI\get;
use function DI\object;

return [

    // Settings that can be customized by users
    'settings.httpVersion' => '1.1',
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering' => 'append',
    'settings.determineRouteBeforeAppMiddleware' => false,
    'settings.displayErrorDetails' => false,

    'settings' => [
        'httpVersion' => get('settings.httpVersion'),
        'responseChunkSize' => get('settings.responseChunkSize'),
        'outputBuffering' => get('settings.outputBuffering'),
        'determineRouteBeforeAppMiddleware' => get('settings.determineRouteBeforeAppMiddleware'),
        'displayErrorDetails' => get('settings.displayErrorDetails'),
    ],

    // Default Slim services
    'router' => object(Slim\Router::class),
    'errorHandler' => object(Slim\Handlers\Error::class)
        ->constructor(get('settings.displayErrorDetails')),
    'notFoundHandler' => object(Slim\Handlers\NotFound::class),
    'notAllowedHandler' => object(Slim\Handlers\NotAllowed::class),
    'environment' => object(Slim\Http\Environment::class)
        ->constructor($_SERVER),

    'request' => factory(function (ContainerInterface $c) {
        return Request::createFromEnvironment($c->get('environment'));
    })->scope(Scope::SINGLETON),
    'response' => factory(function (ContainerInterface $c) {
        $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
        $response = new Response(200, $headers);
        return $response->withProtocolVersion($c->get('settings')['httpVersion']);
    })->scope(Scope::SINGLETON),

    'foundHandler' => object(ControllerInvoker::class)
        ->constructor(get('foundHandler.invoker')),
    'foundHandler.invoker' => function (ContainerInterface $c) {
        $resolvers = [
            new AssociativeArrayResolver,
            new TypeHintContainerResolver($c),
        ];
        return new Invoker(new ResolverChain($resolvers), $c);
    },

    'callableResolver' => object(CallableResolver::class),

    // Aliases
    ContainerInterface::class => get(Container::class),

];
