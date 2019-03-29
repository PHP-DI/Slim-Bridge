<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use DI\Bridge\Slim\Test\Fixture\UserController;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Http\Request;

class RoutingTest extends TestCase
{
    /**
     * @test
     */
    public function injects_request_and_response()
    {
        $app = new App;

        $route1 = '/';

        // Response and request and inversed to check that they are correctly injected by name
        $app->get($route1, function (ResponseInterface $response, ServerRequestInterface $request) {
            var_dump($request->getQueryParams());
            $response->getBody()->write('Hello ' . $request->getQueryParams()['foo']);
            return $response;
        });
        
        $route2 = '/req-res';

        // assert req and res are also injected
        $app->get($route2, function (ResponseInterface $res, ServerRequestInterface $req) {
            var_dump($req->getQueryParams());
            $res->getBody()->write('Hello ' . $req->getQueryParams()['foo']);
            return $res;
        });

        $route3 = '/req-res-by-interface';

        // assert Psr interfaces are also injected
        $app->get($route3, function (ResponseInterface $alt_response, ServerRequestInterface $alt_request) {
            var_dump($alt_request->getQueryParams());
            $alt_response->getBody()->write('Hello ' . $alt_request->getQueryParams()['foo']);
            return $alt_response;
        });

        $route4 = '/req-res-by-class';

        // assert Slim classes are also injected
        $app->get($route4, function (Response $alt_response, Request $alt_request) {
            var_dump($alt_request->getQueryParams());
            $alt_response->getBody()->write('Hello ' . $alt_request->getQueryParams()['foo']);
            return $alt_response;
        });
        
        $response = $app->callMiddlewareStack(RequestFactory::create($route1, 'foo=matt'), new Response);
        $this->assertEquals('Hello matt', $response->getBody()->__toString());
        
        $response = $app->callMiddlewareStack(RequestFactory::create($route2, 'foo=natty'), new Response);
        $this->assertEquals('Hello natty', $response->getBody()->__toString());

        $response = $app->callMiddlewareStack(RequestFactory::create($route3, 'foo=nancy'), new Response);
        $this->assertEquals('Hello nancy', $response->getBody()->__toString());

        $response = $app->callMiddlewareStack(RequestFactory::create($route4, 'foo=nic'), new Response);
        $this->assertEquals('Hello nic', $response->getBody()->__toString());
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
