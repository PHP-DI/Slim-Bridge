<?php

namespace DI\Bridge\Slim;

use Slim\Interfaces\AdvancedCallableResolverInterface;

/**
 * Resolve middleware and route callables using PHP-DI.
 */
class CallableResolver implements AdvancedCallableResolverInterface
{
    /**
     * @var \Invoker\CallableResolver
     */
    private $callableResolver;
    /**
     * @var \Slim\CallableResolver
     */
    private $slimCallableResolver;

    public function __construct(\Invoker\CallableResolver $callableResolver, \Slim\CallableResolver $slimCallableResolver)
    {
        $this->callableResolver = $callableResolver;
        $this->slimCallableResolver = $slimCallableResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($toResolve): callable
    {
        return $this->callableResolver->resolve($toResolve);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRoute($toResolve): callable
    {
        return $this->resolve($toResolve);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveMiddleware($toResolve): callable
    {
        return $this->slimCallableResolver->resolveMiddleware($toResolve);
    }
}
