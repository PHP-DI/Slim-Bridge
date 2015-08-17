<?php

namespace DI\Bridge\Slim;

use Invoker\InvokerInterface;
use Slim\Interfaces\CallableResolverInterface;

/**
 * This class invokes middlewares.
 */
class CallableResolver implements CallableResolverInterface
{
    /**
     * @var InvokerInterface
     */
    private $invoker;

    /**
     * @var callable|string
     */
    private $callable;

    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * Receive a string that is to be resolved to a callable
     *
     * @param string $toResolve
     *
     * @return void
     */
    public function setToResolve($toResolve)
    {
        $this->callable = $toResolve;
    }

    /**
     * Invoke the resolved callable.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke()
    {
        return $this->invoker->call($this->callable, func_get_args());
    }
}
