<?php declare(strict_types=1);

namespace DI\Bridge\Slim\Test\Mock;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;

class RequestFactory
{
    public static function create($uri = '/', $queryString = ''): ServerRequestInterface
    {
        parse_str($queryString, $queryParams);
        return new ServerRequest(
            [],
            [],
            $uri,
            'GET',
            'php://temp',
            [],
            [],
            $queryParams
        );
    }
}
