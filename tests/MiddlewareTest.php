<?php declare(strict_types=1);

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Bridge;
use DI\Bridge\Slim\Test\Fixture\UserMiddlewarePsr;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use Laminas\Diactoros\Response\TextResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function invokes_closure_middleware()
    {
        $app = Bridge::create();
        $app->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return new TextResponse('Hello ' . $request->getQueryParams()['foo']);
        });
        $app->get('/', function () {});

        $response = $app->handle(RequestFactory::create('/', 'foo=matt'));

        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    public function invokes_class_name_middleware()
    {
        $app = Bridge::create();
        $app->add(UserMiddlewarePsr::class);
        $app->get('/', function () {});

        $response = $app->handle(RequestFactory::create('/', 'foo=matt'));

        $this->assertEquals('Hello matt', $response->getBody()->__toString());
    }
}
