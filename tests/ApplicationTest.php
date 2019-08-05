<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Bridge;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use PHPUnit\Framework\TestCase;

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
}
