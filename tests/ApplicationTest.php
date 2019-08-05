<?php

namespace DI\Bridge\Slim\Test;

use App\ContainerFactory;
use DI\Bridge\Slim\App;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApplicationTest extends TestCase
{
    /**
     * @test
     */
    public function runs()
    {
        $app = new App;

        $called = false;
        $app->get('/', function (Request $request, Response $response) use (&$called) {
            $called = true;
            return $response;
        });

        $app->handle(RequestFactory::create());
        $this->assertTrue($called);
    }
}
