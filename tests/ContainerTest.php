<?php

namespace DI\Bridge\Slim\Test;

use DI\Bridge\Slim\Quickstart;
use Slim\App;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Slim expects some container entries to exist. To check that, we get all entries from the
     * default Slim container and check that they exist in PHP-DI.
     *
     * @test
     */
    public function provides_mandatory_container_entries()
    {
        $slimDefault = new App;
        $slimPhpDi = Quickstart::createApplication();

        /** @var \Slim\Container $defaultContainer */
        $defaultContainer = $slimDefault->getContainer();
        /** @var \DI\Container $phpdiContainer */
        $phpdiContainer = $slimPhpDi->getContainer();

        $expectedEntries = $defaultContainer->keys();

        foreach ($expectedEntries as $expectedEntry) {
            $this->assertTrue($phpdiContainer->has($expectedEntry));
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
        $slimDefault = new App;
        $slimPhpDi = Quickstart::createApplication();

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
}
