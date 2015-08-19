<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Quickstart;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class RoutingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function injects_request_and_response()
    {
        $app = Quickstart::createApplication();

        // Response and request and inversed to check that they are correctly injected by name
        $app->get('/', function (ResponseInterface $response, ServerRequestInterface $request) {
            $response->getBody()->write('Hello ' . $request->getQueryParams()['foo']);
            return $response;
        });
        $request = Request::createFromEnvironment(Environment::mock([
            'SCRIPT_NAME'  => 'index.php',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => 'foo=matt',
        ]));
        $response = $app->callMiddlewareStack($request, new Response);
        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    public function injects_request_path_parameters()
    {
        $app = Quickstart::createApplication();

        $app->get('/{name}', function ($name, $response) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });
        $request = Request::createFromEnvironment(Environment::mock([
            'SCRIPT_NAME'  => 'index.php',
            'REQUEST_URI'  => '/matt',
        ]));
        $response = $app->callMiddlewareStack($request, new Response);
        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    public function resolve_controller_from_container()
    {
        $app = Quickstart::createApplication();

        $app->get('/', ['DI\Bridge\Slim\Test\Fixture\UserController', 'dashboard']);
        $request = Request::createFromEnvironment(Environment::mock([
            'SCRIPT_NAME'  => 'index.php',
            'REQUEST_URI'  => '/',
        ]));
        $response = $app->callMiddlewareStack($request, new Response);
        $this->assertEquals('Hello world!', $response->getBody()->__toString());
    }
}
