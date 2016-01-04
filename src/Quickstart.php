<?php

namespace DI\Bridge\Slim;

use DI\Container;
use DI\Kernel\Kernel;
use Silly\Edition\PhpDi\Application;
use Slim\App;

/**
 * Creates pre-configured classes to get started easily.
 */
class Quickstart
{
    /**
     * @return Container
     */
    public static function container()
    {
        $kernel = new Kernel();
        return $kernel->createContainer();
    }

    /**
     * Create a Slim web application.
     *
     * @return App
     */
    public static function web()
    {
        return new App(self::container());
    }

    /**
     * Create a Silly application for the command line.
     *
     * @return Application
     */
    public static function cli()
    {
        return new Application('UNKNOWN', 'UNKNOWN', self::container());
    }
}
