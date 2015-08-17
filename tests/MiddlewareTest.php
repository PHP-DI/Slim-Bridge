<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Quickstart;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class MiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function invokes_closure_middleware()
    {
        $app = Quickstart::createApplication();

        $app->addMiddleware(function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
            $response->getBody()->write('Hello ' . $request->getQueryParams()['foo']);
            return $response;
        });

        $app->get('/', function () {});
        $request = Request::createFromEnvironment(Environment::mock([
            'SCRIPT_NAME'  => 'index.php',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => 'foo=matt',
        ]));
        $response = new Response;
        $response = $app->callMiddlewareStack($request, $response);
        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }
}
