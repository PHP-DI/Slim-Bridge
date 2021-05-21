<?php declare(strict_types=1);

namespace DI\Bridge\Slim\Test\Fixture;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{
    public function dashboard(ResponseInterface $response)
    {
        $response->getBody()->write('Hello world!');

        return $response;
    }

    /**
     * @param ServerRequestInterface $request PSR-7 request
     * @param ResponseInterface $response PSR-7 response
     * @param array $args The route's placeholder arguments
     *
     * @return ResponseInterface
     */
    public function invite(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $str = join(' ', $args);
        $response->getBody()->write("Hello $str!");

        return $response;
    }
}
