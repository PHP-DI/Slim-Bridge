<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\App;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ContainerTest extends TestCase
{
    /**
     * Slim expects some container entries to exist. To check that, we get all entries from the
     * default Slim container and check that they exist in PHP-DI.
     *
     * @test
     */
    public function provides_mandatory_container_entries()
    {
        $slimDefault = new \Slim\App;
        $slimPhpDi = new App;

        /** @var \Slim\Container $defaultContainer */
        $defaultContainer = $slimDefault->getContainer();
        /** @var \DI\Container $phpdiContainer */
        $phpdiContainer = $slimPhpDi->getContainer();

        $expectedEntries = $defaultContainer->keys();

        foreach ($expectedEntries as $expectedEntry) {
            $this->assertTrue($phpdiContainer->has($expectedEntry), "Container entry $expectedEntry is missing");
            // Check that the service is created without exception
            $phpdiContainer->get($expectedEntry);
        }
    }

    /**
     * Slim expects some config options to exist.
     *
     * @test
     */
    public function provides_default_config_options()
    {
        $slimDefault = new \Slim\App;
        $slimPhpDi = new App;

        /** @var \Slim\Container $defaultContainer */
        $defaultContainer = $slimDefault->getContainer();
        /** @var \DI\Container $phpdiContainer */
        $phpdiContainer = $slimPhpDi->getContainer();

        $expectedOptions = $defaultContainer->get('settings');
        $actualOptions = $phpdiContainer->get('settings');

        foreach ($expectedOptions as $name => $value) {
            $this->assertArrayHasKey($name, $actualOptions);
            // Has the same default value
            $this->assertEquals($value, $actualOptions[$name]);
        }
    }

    /**
     * Makes sure Request definitions can be injected.
     *
     * @test
     */
    public function provides_request_objects()
    {
        $slimPhpDi = new App;

        /** @var \DI\Container $phpdiContainer */
        $phpdiContainer = $slimPhpDi->getContainer();

        $request = $phpdiContainer->get('request');
        $this->assertNotNull($request);
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        
        $res = $phpdiContainer->get('req');
        $this->assertNotNull($res);
        $this->assertInstanceOf(ServerRequestInterface::class, $res);
        
        $request_interface = $phpdiContainer->get(ServerRequestInterface::class);
        $this->assertNotNull($request_interface);
        $this->assertInstanceOf(ServerRequestInterface::class, $request_interface);

        $request_slim = $phpdiContainer->get(Request::class);
        $this->assertNotNull($request_slim);
        $this->assertInstanceOf(ServerRequestInterface::class, $request_slim);
    }

    /**
     * Makes sure Response definitions can be injected.
     *
     * @test
     */
    public function provides_response_objects()
    {
        $slimPhpDi = new App;

        /** @var \DI\Container $phpdiContainer */
        $phpdiContainer = $slimPhpDi->getContainer();

        $response = $phpdiContainer->get('response');
        $this->assertNotNull($response);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        
        $res = $phpdiContainer->get('res');
        $this->assertNotNull($res);
        $this->assertInstanceOf(ResponseInterface::class, $res);
        
        $response_interface = $phpdiContainer->get(ResponseInterface::class);
        $this->assertNotNull($response_interface);
        $this->assertInstanceOf(ResponseInterface::class, $response_interface);

        $response_slim = $phpdiContainer->get(Response::class);
        $this->assertNotNull($response_slim);
        $this->assertInstanceOf(ResponseInterface::class, $response_slim);
    }
}
