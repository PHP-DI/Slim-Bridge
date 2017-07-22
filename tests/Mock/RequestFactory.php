<?php

namespace DI\Bridge\Slim\Test\Mock;

use Slim\Http\Environment;
use Slim\Http\Request;

class RequestFactory
{
    public static function create($uri = '/', $queryString = '')
    {
        return Request::createFromGlobals(Environment::mock([
            'SCRIPT_NAME'  => 'index.php',
            'REQUEST_URI'  => $uri,
            'QUERY_STRING' => $queryString,
        ]));
    }
}
