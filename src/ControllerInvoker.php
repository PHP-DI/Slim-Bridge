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

    /** @var bool Whether attributes should override parameters */
    protected $prioritiseAttributesOverParams;

    public function __construct(InvokerInterface $invoker, bool $prioritiseAttributesOverParams)
    {
        $this->invoker = $invoker;
        $this->prioritiseAttributesOverParams = $prioritiseAttributesOverParams;
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
            'request'  => self::injectRouteArguments($request, $routeArguments, $this->prioritiseAttributesOverParams),
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
    protected static function injectRouteArguments(
        ServerRequestInterface $request,
        array $routeArguments,
        bool $prioritiseAttributesOverParams
    ): ServerRequestInterface {
        $requestWithArgs = $request;
        foreach ($routeArguments as $key => $value) {
            if ($prioritiseAttributesOverParams && $request->getAttribute($key) !== null) {
                continue;
            }
            $requestWithArgs = $requestWithArgs->withAttribute($key, $value);
        }
        return $requestWithArgs;
    }
}
