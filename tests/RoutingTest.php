<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use DI\Bridge\Slim\Test\Fixture\UserController;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class RoutingTest extends \PHPUnit\Framework\TestCase
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
    public function injects_request_path_parameters()
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
    public function injects_optional_path_parameter()
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
    public function injects_default_value_in_optional_path_parameter()
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
    public function resolve_controller_from_container()
    {
        $app = new App;
        $app->get('/', [UserController::class, 'dashboard']);

        $response = $app->callMiddlewareStack(RequestFactory::create(), new Response);
        $this->assertEquals('Hello world!', $response->getBody()->__toString());
    }
}
