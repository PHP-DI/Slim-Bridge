<?php

use DI\Bridge\Slim\CallableResolver;
use DI\Bridge\Slim\ControllerInvoker;
use DI\Container;
use Interop\Container\ContainerInterface;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;

return [

    // Settings that can be customized by users
    'settings.httpVersion' => '1.1',
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering' => 'append',
    'settings.determineRouteBeforeAppMiddleware' => false,
    'settings.displayErrorDetails' => false,
    'settings.addContentLengthHeader' => true,
    'settings.routerCacheFile' => false,

    'settings' => [
        'httpVersion' => DI\get('settings.httpVersion'),
        'responseChunkSize' => DI\get('settings.responseChunkSize'),
        'outputBuffering' => DI\get('settings.outputBuffering'),
        'determineRouteBeforeAppMiddleware' => DI\get('settings.determineRouteBeforeAppMiddleware'),
        'displayErrorDetails' => DI\get('settings.displayErrorDetails'),
        'addContentLengthHeader' => DI\get('settings.addContentLengthHeader'),
        'routerCacheFile' => DI\get('settings.routerCacheFile'),
    ],

    // Default Slim services
    'router' => DI\object(Slim\Router::class)
        ->method('setCacheFile', DI\get('settings.routerCacheFile')),
    Slim\Router::class => DI\get('router'),
    'errorHandler' => DI\object(Slim\Handlers\Error::class)
        ->constructor(DI\get('settings.displayErrorDetails'), DI\get('settings.outputBuffering')),
    'phpErrorHandler' => DI\object(Slim\Handlers\PhpError::class)
        ->constructor(DI\get('settings.displayErrorDetails'), DI\get('settings.outputBuffering')),
    'notFoundHandler' => DI\object(Slim\Handlers\NotFound::class),
    'notAllowedHandler' => DI\object(Slim\Handlers\NotAllowed::class),
    'environment' => function () {
        return new Slim\Http\Environment($_SERVER);
    },
    'request' => function (ContainerInterface $c) {
        return Request::createFromEnvironment($c->get('environment'));
    },
    'response' => function (ContainerInterface $c) {
        $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
        $response = new Response(200, $headers);
        return $response->withProtocolVersion($c->get('settings')['httpVersion']);
    },
    'foundHandler' => DI\object(ControllerInvoker::class)
        ->constructor(DI\get('foundHandler.invoker')),
    'foundHandler.invoker' => function (ContainerInterface $c) {
        $resolvers = [
            // Inject parameters by name first
            new AssociativeArrayResolver,
            // Then inject services by type-hints for those that weren't resolved
            new TypeHintContainerResolver($c),
            // Then fall back on parameters default values for optional route parameters
            new DefaultValueResolver(),
        ];
        return new Invoker(new ResolverChain($resolvers), $c);
    },

    'callableResolver' => DI\object(CallableResolver::class),

    // Aliases
    ContainerInterface::class => DI\get(Container::class),

];
