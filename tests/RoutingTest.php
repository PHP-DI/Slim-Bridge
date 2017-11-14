<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use DI\Bridge\Slim\Test\Fixture\UserController;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

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

        $response = $app->callMiddlewareStack(RequestFactory::create('/', 'foo=matt'), new Response);
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

        $response = $app->callMiddlewareStack(RequestFactory::create('/matt'), new Response);
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

        $response = $app->callMiddlewareStack(RequestFactory::create('/matt'), new Response);
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

        $response = $app->callMiddlewareStack(RequestFactory::create('/'), new Response);
        $this->assertEquals('Hello john doe', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_request_attribute()
    {
        $app = new App;
        // Let's add a middleware that adds a request attribute
        $app->add(function (ServerRequestInterface $request, $response, $next) {
            return $next($request->withAttribute('name', 'Bob'), $response);
        });
        $app->get('/', function ($name, $response) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->callMiddlewareStack(RequestFactory::create('/'), new Response);
        $this->assertEquals('Hello Bob', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    public function injects_route_placeholder_over_request_attribute()
    {
        $app = new App;
        $app->add(function (ServerRequestInterface $request, $response, $next) {
            return $next($request->withAttribute('name', 'Bob'), $response);
        });
        $app->get('/{name}', function ($name, $response) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->callMiddlewareStack(RequestFactory::create('/matt'), new Response);
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

        $response = $app->callMiddlewareStack(RequestFactory::create(), new Response);
        $this->assertEquals('Hello world!', $response->getBody()->__toString());
    }
}
