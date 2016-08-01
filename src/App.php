<?php

namespace DI\Bridge\Slim;

use DI\ContainerBuilder;
use UnexpectedValueException;
use RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;



/**
 * Slim application configured with PHP-DI.
 *
 * As you can see, this class is very basic and is only useful to get started quickly.
 * You can also very well *not* use it and build the container manually.
 */
class App extends \Slim\App
{
    public function __construct()
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->addDefinitions(__DIR__ . '/config.php');
        $this->configureContainer($containerBuilder);
        $container = $containerBuilder->build();

        parent::__construct($container);
    }

    protected function addMiddleware(callable $callable){
        if ($this->middlewareLock) {
            throw new RuntimeException('Middleware can’t be added once the stack is dequeuing');
        }

        if (is_null($this->stack)) {
            $this->seedMiddlewareStack();
        }

        $next = $this->stack->top();
        $this->stack[] = function (ServerRequestInterface $req, ResponseInterface $res) use ($callable, $next){
            $result = call_user_func($this->getContainer()->get('foundHandler'),$callable, $req, $res,['next' => $next]);
            if ($result instanceof ResponseInterface === false) {
                throw new UnexpectedValueException(
                    'Middleware must return instance of \Psr\Http\Message\ResponseInterface'
                );
            }

            return $result;
        };

        return $this;
    }

    public function add($callable)
    {
        return $this->addMiddleware($callable);
    }

    /**
     * Override this method to configure the container builder.
     *
     * For example, to load additional configuration files:
     *
     *     protected function configureContainer(ContainerBuilder $builder)
     *     {
     *         $builder->addDefinitions(__DIR__ . 'my-config-file.php');
     *     }
     */
    protected function configureContainer(ContainerBuilder $builder)
    {
    }
}
