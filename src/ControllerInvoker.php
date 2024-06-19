<?php declare(strict_types=1);

namespace DI\Bridge\Slim;

use Invoker\InvokerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;

class ControllerInvoker implements InvocationStrategyInterface
{
    /** @var InvokerInterface */
    private $invoker;

    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * Invoke a route callable.
     *
     * @param callable               $callable       The callable to invoke using the strategy.
     * @param ServerRequestInterface $request        The request object.
     * @param ResponseInterface      $response       The response object.
     * @param array                  $routeArguments The route's placeholder arguments
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        // Inject the request and response by parameter name
        $parameters = [
            'request'  => self::injectRouteArguments($request, $routeArguments),
            'response' => $response,
        ];
        // Inject the route arguments by name
        $parameters += $routeArguments;
        // Inject the attributes defined on the request
        $parameters += $request->getAttributes();

        return $this->processResponse($this->invoker->call($callable, $parameters));
    }

    /**
     * Allow for child classes to process the response.
     *
     * @param ResponseInterface|string $response The response from the callable.
     * @return ResponseInterface|string The processed response
     */
    protected function processResponse($response)
    {
        return $response;
    }

    /**
     * Inject route arguments into the request.
     *
     * @param array                  $routeArguments
     */
    protected static function injectRouteArguments(ServerRequestInterface $request, array $routeArguments): ServerRequestInterface
    {
        $requestWithArgs = $request;
        foreach ($routeArguments as $key => $value) {
            $requestWithArgs = $requestWithArgs->withAttribute($key, $value);
        }
        return $requestWithArgs;
    }
}
