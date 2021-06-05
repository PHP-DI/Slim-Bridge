<?php

declare(strict_types=1);

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Bridge;
use DI\Bridge\Slim\Test\Fixture\UserController;
use DI\Bridge\Slim\Test\Fixture\UserControllerInvokable;
use DI\Bridge\Slim\Test\Fixture\UserControllerPsr;
use DI\Bridge\Slim\Test\Fixture\UserMiddlewarePsr;
use DI\Container;
use PHPUnit\Framework\TestCase;

use Slim\Interfaces\AdvancedCallableResolverInterface;

use function DI\get;


class CallableTest extends TestCase
{
    /**
     * @test
     */
    public function resolves_callable()
    {
        $app      = Bridge::create();
        $resolver = $app->getCallableResolver();

        /** @var Container $container */
        $container = $app->getContainer();
        $container->set('controller.user', get(UserController::class));

        [$object, $method] = $resolver->resolve(UserController::class . ':dashboard');
        $this->assertInstanceOf(UserController::class, $object);
        $this->assertEquals('dashboard', $method);

        [$object, $method] = $resolver->resolve('controller.user:dashboard');
        $this->assertInstanceOf(UserController::class, $object);
        $this->assertEquals('dashboard', $method);

        [$object, $method] = $resolver->resolve(UserController::class . '::dashboard');
        $this->assertInstanceOf(UserController::class, $object);
        $this->assertEquals('dashboard', $method);

        [$object, $method] = $resolver->resolve([UserController::class, 'dashboard']);
        $this->assertInstanceOf(UserController::class, $object);
        $this->assertEquals('dashboard', $method);

        $resolved = $resolver->resolve(UserControllerInvokable::class);
        $this->assertInstanceOf(UserControllerInvokable::class, $resolved);
    }

    /**
     * @test
     */
    public function resolves_route()
    {
        $app = Bridge::create();
        /** @var AdvancedCallableResolverInterface $resolver */
        $resolver = $app->getCallableResolver();

        /** @var Container $container */
        $container = $app->getContainer();
        $container->set('controller.userpsr', get(UserControllerPsr::class));

        [$object, $method] = $resolver->resolveRoute(UserControllerPsr::class);
        $this->assertInstanceOf(UserControllerPsr::class, $object);
        $this->assertEquals('handle', $method);

        [$object, $method] = $resolver->resolveRoute('controller.userpsr');
        $this->assertInstanceOf(UserControllerPsr::class, $object);
        $this->assertEquals('handle', $method);
    }

    /**
     * @test
     */
    public function resolves_middleware()
    {
        $app      = Bridge::create();
        /** @var AdvancedCallableResolverInterface $resolver */
        $resolver = $app->getCallableResolver();

        [$object, $method] = $resolver->resolveMiddleware(UserMiddlewarePsr::class);
        $this->assertInstanceOf(UserMiddlewarePsr::class, $object);
        $this->assertEquals('process', $method);
    }
}
