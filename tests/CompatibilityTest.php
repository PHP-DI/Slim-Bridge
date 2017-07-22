<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\CallableResolver;
use DI\Bridge\Slim\ControllerInvoker;
use DI\Bridge\Slim\App;

use DI\Bridge\Slim\Test\Mock\RequestFactory;
use Slim\Http\Response;

class CompatibilityTest extends \PHPUnit\Framework\TestCase
{

    private $config = [
        'settings.routerCacheFile' => __DIR__ . '/routerCache.php',
    ];

    public function tearDown()
    {
        if (file_exists($this->config['settings.routerCacheFile'])) {
            unlink($this->config['settings.routerCacheFile']);
        }
    }

    /**
     * @test
     */
    public function router_configured()
    {
        $app = new App($this->config);

        $app->get('/', function () {
            return new Response;
        });

        $app->callMiddlewareStack(RequestFactory::create(), new Response);

        $routes = $app->getRouter()->getRoutes();
        $this->assertNotEmpty($routes);
        foreach ($routes as $route) {
            $this->assertEquals(get_class($route->getCallableResolver()), CallableResolver::class);
            $this->assertEquals(get_class($route->getInvocationStrategy()), ControllerInvoker::class);
        }
        $this->assertTrue(file_exists($this->config['settings.routerCacheFile']));
    }
}
