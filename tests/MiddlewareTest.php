<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Quickstart;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class MiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function invokes_closure_middleware()
    {
        $app = Quickstart::createApplication();
        $app->add(function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
            $response->getBody()->write('Hello ' . $request->getQueryParams()['foo']);
            return $response;
        });
        $app->get('/', function () {});

        $response = $app->callMiddlewareStack(RequestFactory::create('/', 'foo=matt'), new Response);

        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }
}
