<?php declare(strict_types=1);

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Bridge;
use DI\Bridge\Slim\Test\Fixture\UserController;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingTest extends TestCase
{
    /**
     * @test
     */
    public function injects_request_and_response()
    {
        $app = Bridge::create();

        // Response and request and inversed to check that they are correctly injected by name
        $app->get('/', function (ResponseInterface $response, ServerRequestInterface $request) {
            $response->getBody()->write('Hello ' . $request->getQueryParams()['foo']);
            return $response;
        });

        $response = $app->handle(RequestFactory::create('/', 'foo=matt'));
        $this->assertEquals('Hello matt', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_route_placeholder()
    {
        $app = Bridge::create();
        $app->get('/{name}', function ($name, $response) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->handle(RequestFactory::create('/matt'));
        $this->assertEquals('Hello matt', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_optional_route_placeholder()
    {
        $app = Bridge::create();
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
        $app = Bridge::create();
        $app->get('/[{name}]', function ($response, $name = 'john doe') {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->handle(RequestFactory::create());
        $this->assertEquals('Hello john doe', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_request_attribute()
    {
        $app = Bridge::create();
        // Let's add a middleware that adds a request attribute
        $app->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request->withAttribute('name', 'Bob'));
        });
        $app->get('/', function ($name, $response) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->handle(RequestFactory::create());
        $this->assertEquals('Hello Bob', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_route_placeholder_over_request_attribute()
    {
        $app = Bridge::create();
        $app->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request->withAttribute('name', 'Bob'));
        });
        $app->get('/{name}', function ($name, $response) {
            $response->getBody()->write('Hello ' . $name);
            return $response;
        });

        $response = $app->handle(RequestFactory::create('/matt'));
        // The route placeholder has priority over the request attribute
        $this->assertEquals('Hello matt', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function resolve_controller_from_container()
    {
        $app = Bridge::create();
        $app->get('/', [UserController::class, 'dashboard']);

        $response = $app->handle(RequestFactory::create());
        $this->assertEquals('Hello world!', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function injects_route_placeholders_array_to_resolved_controller_from_container()
    {
        $app = Bridge::create();
        $app->get('/{prefix}/{name}', [UserController::class, 'invite']);

        $response = $app->handle(RequestFactory::create('/dear/slim'));
        $this->assertEquals('Hello dear slim!', (string) $response->getBody());
    }

}
