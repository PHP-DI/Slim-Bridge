<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Quickstart;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function runs()
    {
        $app = Quickstart::createApplication();

        $called = false;
        $app->get('/', function () use (&$called) {
            $called = true;
        });
        $request = Request::createFromEnvironment(Environment::mock([
            'SCRIPT_NAME' => 'index.php',
            'REQUEST_URI' => '/',
        ]));
        $response = new Response;
        $app->callMiddlewareStack($request, $response);
        $this->assertTrue($called);
    }
}
