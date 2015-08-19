<?php

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

    'settings.cookieLifetime'    => '20 minutes',
    'settings.cookiePath'        => '/',
    'settings.cookieDomain'      => null,
    'settings.cookieSecure'      => false,
    'settings.cookieHttpOnly'    => false,
    'settings.httpVersion'       => '1.1',
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering'   => 'append',

    'settings' => [
        'cookieLifetime'    => DI\get('settings.cookieLifetime'),
        'cookiePath'        => DI\get('settings.cookiePath'),
        'cookieDomain'      => DI\get('settings.cookieDomain'),
        'cookieSecure'      => DI\get('settings.cookieSecure'),
        'cookieHttpOnly'    => DI\get('settings.cookieHttpOnly'),
        'httpVersion'       => DI\get('settings.httpVersion'),
        'responseChunkSize' => DI\get('settings.responseChunkSize'),
        'outputBuffering'   => DI\get('settings.outputBuffering'),
    ],

    'router'            => DI\object('Slim\Router'),
    'errorHandler'      => DI\object('Slim\Handlers\Error'),
    'notFoundHandler'   => DI\object('Slim\Handlers\NotFound'),
    'notAllowedHandler' => DI\object('Slim\Handlers\NotAllowed'),
    'environment'       => DI\object('Slim\Http\Environment')
        ->constructor($_SERVER),

    'request' => DI\factory(function (ContainerInterface $c) {
        return Request::createFromEnvironment($c->get('environment'));
    })->scope(Scope::SINGLETON),
    'response' => DI\factory(function (ContainerInterface $c) {
        $headers = new Headers(['Content-Type' => 'text/html']);
        $response = new Response(200, $headers);
        return $response->withProtocolVersion($c->get('settings')['httpVersion']);
    })->scope(Scope::SINGLETON),

    'foundHandler'         => DI\object('DI\Bridge\Slim\ControllerInvoker')
        ->constructor(DI\get('foundHandler.invoker')),
    'foundHandler.invoker' => function (ContainerInterface $c) {
        $resolvers = [
            new AssociativeArrayResolver,
            new TypeHintContainerResolver($c),
        ];
        return new Invoker(new ResolverChain($resolvers), $c);
    },

    'callableResolver' => DI\object('DI\Bridge\Slim\CallableResolver'),

    'Interop\Container\ContainerInterface' => DI\get('DI\Container'),

];
