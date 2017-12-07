<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use DI\ContainerBuilder;
use Slim\Handlers\Error;
use PHPUnit\Framework\TestCase;
use DI\Bridge\Slim\Test\Mock\RequestFactory;
use Slim\Http\Response;

class ErrorTest extends TestCase
{
    /**
     * Test default errorHandler
     *
     * @test
     */
    public function default_exception_handling()
    {
        // Send error_log output to a temp file.
        $logFile = tempnam(sys_get_temp_dir(), 'slim-bridge');
        ini_set('error_log', $logFile);

        $app = new App;
        $c = $app->getContainer();

        // Sanity check - default for displayErrorDetails should be false
        $displayErrorDetails = $c->get('settings.displayErrorDetails');
        $this->assertFalse($displayErrorDetails);

        /** @var Error $error */
        $error = $c->get('errorHandler');
        $response = $error(RequestFactory::create('/'), new Response(), new \Exception());
        $reasonPhrase = $response->getReasonPhrase();
        $this->assertEquals('Internal Server Error', $reasonPhrase);

        $log = file_get_contents($logFile);
        $this->assertNotEmpty($log);
    }

    /**
     * Test custom errorHandler
     *
     * @test
     */
    public function custom_exception_handling()
    {
        // Send error_log output to a temp file.
        $logFile = tempnam(sys_get_temp_dir(), 'slim-bridge');
        ini_set('error_log', $logFile);

        $app = new BridgeApp(
            [
                'settings.displayErrorDetails' => true,
                'settings.outputBuffering' => 'append'
            ]);
        $c = $app->getContainer();

        // Sanity checks
        $displayErrorDetails = $c->get('settings.displayErrorDetails');
        $this->assertTrue($displayErrorDetails);
        $outputBuffering = $c->get('settings.outputBuffering');
        $this->assertEquals('append', $outputBuffering);

        /** @var Error $error */
        $error = $c->get('errorHandler');

        $response = $error(RequestFactory::create('/'), new Response(), new \Exception());
        $reasonPhrase = $response->getReasonPhrase();
        $this->assertEquals('Internal Server Error', $reasonPhrase);

        $log = file_get_contents($logFile);
        $this->assertEmpty($log);
    }
}

/**
 * Class BridgeApp
 *
 * Override the configuration via the configureContainer() hook.
 */
class BridgeApp extends App
{
    protected $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;

        parent::__construct();
    }

    public function configureContainer(ContainerBuilder $builder)
    {
        $builder->addDefinitions($this->config);
    }
}
