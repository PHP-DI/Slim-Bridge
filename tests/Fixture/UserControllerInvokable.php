<?php declare(strict_types=1);

namespace DI\Bridge\Slim\Test\Fixture;

use Psr\Http\Message\ResponseInterface;

class UserControllerInvokable
{
    public function __invoke(ResponseInterface $response)
    {
        $response->getBody()->write('Hello world!');

        return $response;
    }
}
