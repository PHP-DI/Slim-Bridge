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

return [

    // Settings that can be customized by users
    'settings.cookieLifetime'                    => '20 minutes',
    'settings.cookiePath'                        => '/',
    'settings.cookieDomain'                      => null,
    'settings.cookieSecure'                      => false,
    'settings.cookieHttpOnly'                    => false,
    'settings.httpVersion'                       => '1.1',
    'settings.responseChunkSize'                 => 4096,
    'settings.outputBuffering'                   => 'append',
    'settings.determineRouteBeforeAppMiddleware' => false,
    'settings.displayErrorDetails' => false,

    'settings' => [
        'cookieLifetime'                    => DI\get('settings.cookieLifetime'),
        'cookiePath'                        => DI\get('settings.cookiePath'),
        'cookieDomain'                      => DI\get('settings.cookieDomain'),
        'cookieSecure'                      => DI\get('settings.cookieSecure'),
        'cookieHttpOnly'                    => DI\get('settings.cookieHttpOnly'),
        'httpVersion'                       => DI\get('settings.httpVersion'),
        'responseChunkSize'                 => DI\get('settings.responseChunkSize'),
        'outputBuffering'                   => DI\get('settings.outputBuffering'),
        'determineRouteBeforeAppMiddleware' => DI\get('settings.determineRouteBeforeAppMiddleware'),
        'displayErrorDetails' => DI\get('settings.displayErrorDetails'),
    ],

    // Default Slim services
    'router'            => DI\object(Slim\Router::class),
    'errorHandler'      => DI\object(Slim\Handlers\Error::class),
    'notFoundHandler'   => DI\object(Slim\Handlers\NotFound::class),
    'notAllowedHandler' => DI\object(Slim\Handlers\NotAllowed::class),
    'environment'       => DI\object(Slim\Http\Environment::class)
        ->constructor($_SERVER),

    'request' => DI\factory(function (ContainerInterface $c) {
        return Request::createFromEnvironment($c->get('environment'));
    })->scope(Scope::SINGLETON),
    'response' => DI\factory(function (ContainerInterface $c) {
        $headers = new Headers(['Content-Type' => 'text/html']);
        $response = new Response(200, $headers);
        return $response->withProtocolVersion($c->get('settings')['httpVersion']);
    })->scope(Scope::SINGLETON),

    'foundHandler'         => DI\object(ControllerInvoker::class)
        ->constructor(DI\get('foundHandler.invoker')),
    'foundHandler.invoker' => function (ContainerInterface $c) {
        $resolvers = [
            new AssociativeArrayResolver,
            new TypeHintContainerResolver($c),
        ];
        return new Invoker(new ResolverChain($resolvers), $c);
    },

    'callableResolver' => DI\object(CallableResolver::class),

    // Aliases
    ContainerInterface::class => DI\get(Container::class),

];
