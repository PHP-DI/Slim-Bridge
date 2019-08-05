<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use DI\Bridge\Slim\Test\Fixture\UserController;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class RoutingTest extends TestCase
{
    /**
     * @test
     */
    public function injects_request_and_response()
    {
        $app = new App;
        // Response and request and inversed to check that they are correctly injected by name
        $app->get('/', function (ResponseInterface $response, ServerRequestInterface $request) {
            $response->getBody()->write('Hello ' . $request->getQueryParams()['foo']);
            return $response;
        });

        $response = $app->handle(RequestFactory::create('/', ['foo' => 'matt']));
        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    public function injects_route_placeholder()
    {
        $app = new App;
        $app->get('/{name}', function ($name, $response) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->handle(RequestFactory::create('/matt'));
        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    public function injects_optional_route_placeholder()
    {
        $app = new App;
        $app->get('/[{name}]', function ($response, $name = null) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->handle(RequestFactory::create('/matt'));
        $this->assertEquals('Hello matt', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_default_value_in_optional_route_placeholder()
    {
        $app = new App;

        $app->get('/[{name}]', function ($response, $name = 'john doe') {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->handle(RequestFactory::create('/'));
        $this->assertEquals('Hello john doe', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_request_attribute()
    {
        $app = new App;

        //todo: move in middleware
        $request = RequestFactory::create('/');
        $request = $request->withAttribute('name', 'Bob');

        $app->get('/', function ($name, Request $request, Response $response) {
            $response->getBody()->write("Hello $name");
            return $response;
        });

        $response = $app->handle($request);
        $this->assertEquals('Hello Bob', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_route_placeholder_over_request_attribute()
    {
        $app = new App;

        //todo: move in middleware
        $request = RequestFactory::create('/matt');
        $request = $request->withAttribute('name', 'Bob');

        $app->get('/{name}', function ($name, $response) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->handle($request);
        // The route placeholder has priority over the request attribute
        $this->assertEquals('Hello matt', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function resolve_controller_from_container()
    {
        $app = new App;
        $app->get('/', [UserController::class, 'dashboard']);

        $response = $app->handle(RequestFactory::create());
        $this->assertEquals('Hello world!', $response->getBody()->__toString());
    }
}
