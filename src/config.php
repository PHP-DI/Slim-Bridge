<?php

use function DI\autowire;
use DI\Bridge\Slim\CallableResolver;
use DI\Bridge\Slim\ControllerInvoker;
use DI\Container;
use function DI\create;
use function DI\get;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
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
        'httpVersion' => get('settings.httpVersion'),
        'responseChunkSize' => get('settings.responseChunkSize'),
        'outputBuffering' => get('settings.outputBuffering'),
        'determineRouteBeforeAppMiddleware' => get('settings.determineRouteBeforeAppMiddleware'),
        'displayErrorDetails' => get('settings.displayErrorDetails'),
        'addContentLengthHeader' => get('settings.addContentLengthHeader'),
        'routerCacheFile' => get('settings.routerCacheFile'),
    ],

    // Default Slim services
    'router' => create(Slim\Router::class)
        ->method('setContainer', get(Container::class))
        ->method('setCacheFile', get('settings.routerCacheFile')),
    Slim\Router::class => get('router'),
    'errorHandler' => create(Slim\Handlers\Error::class)
        ->constructor(get('settings.displayErrorDetails')),
    'phpErrorHandler' => create(Slim\Handlers\PhpError::class)
        ->constructor(get('settings.displayErrorDetails')),
    'notFoundHandler' => create(Slim\Handlers\NotFound::class),
    'notAllowedHandler' => create(Slim\Handlers\NotAllowed::class),
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
    'foundHandler' => create(ControllerInvoker::class)
        ->constructor(get('foundHandler.invoker')),
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

    'callableResolver' => autowire(CallableResolver::class),

];
