<?php

namespace DI\Bridge\Slim\Test\Mock;

use Slim\Psr7\Environment;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;

class RequestFactory
{
    public static function create($uri = '/', $queryParams = null)
    {
        $request = new Request(
            'GET',
            (new UriFactory())->createUri($uri),
            new Headers([]),
            [],
            [],
            (new StreamFactory())->createStream()
        );

        if ($queryParams) {
            $request = $request->withQueryParams($queryParams);
        }

        return $request;
    }
}
