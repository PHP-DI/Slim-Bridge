<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Quickstart;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class RouteTest extends \PHPUnit_Framework_TestCase
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
        });
        $request = Request::createFromEnvironment(Environment::mock([
            'SCRIPT_NAME'  => 'index.php',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => 'foo=matt',
        ]));
        $response = new Response;
        $response = $app->callMiddlewareStack($request, $response);
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
        });
        $request = Request::createFromEnvironment(Environment::mock([
            'SCRIPT_NAME'  => 'index.php',
            'REQUEST_URI'  => '/matt',
        ]));
        $response = new Response;
        $response = $app->callMiddlewareStack($request, $response);
        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }
}
