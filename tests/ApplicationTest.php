<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use Slim\Http\Response;

class ApplicationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function runs()
    {
        $app = new App;

        $called = false;
        $app->get('/', function () use (&$called) {
            $called = true;
            // Route handler must return Response in Slim 4.x
            return new Response;
        });

        $app->callMiddlewareStack(RequestFactory::create(), new Response);
        $this->assertTrue($called);
    }
}
