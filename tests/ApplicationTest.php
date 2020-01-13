<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Bridge;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use PHPUnit\Framework\TestCase;
use DI\ContainerBuilder;
use Slim\App;
use Psr\Http\Message\ResponseInterface;

class ApplicationTest extends TestCase
{
    /**
     * @test
     */
    public function runs()
    {
        $app = Bridge::create();

        $called = false;
        $app->get('/', function ($request, $response) use (&$called) {
            $called = true;
            return $response;
        });
        $app->handle(RequestFactory::create());

        $this->assertTrue($called);
    }

    /**
     * @test
     */
    public function register_app_instance_to_container()
    {
        $app = Bridge::create();

        $instance = null;

        $app->get('/', function (App $app, ResponseInterface $response) use(&$instance) {
            $instance = $app;
            return $response;
        });

        $app->handle(RequestFactory::create());

        $this->assertInstanceOf(
            App::class,
            $instance
        );
    }
}
