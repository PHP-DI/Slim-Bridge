<?php

namespace DI\Bridge\Slim\Test\Fixture;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Middleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $response->getBody()->write('Hello world!');
        return $response;
    }
}
