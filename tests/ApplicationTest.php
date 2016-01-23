<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use Slim\Http\Response;

class ApplicationTest extends \PHPUnit_Framework_TestCase
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
        });

        $app->callMiddlewareStack(RequestFactory::create(), new Response);
        $this->assertTrue($called);
    }
}
