<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function invokes_closure_middleware()
    {
        $app = new App;

        $app->add(function (Request $request, RequestHandler $handler) {
            $response = new Response();
            $response->getBody()->write('Hello ' . $request->getQueryParams()['foo']);

            return $response;
        });
        $app->get('/', function () {});

        $response = $app->handle(RequestFactory::create('/', ['foo' => 'matt']));
        echo $response->getBody()->__toString();
        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }
}
