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
}
