<?php

require 'vendor/autoload.php';

$app = \DI\Bridge\Slim\Quickstart::createApplication();

$app->get('/', function () {
    return 'Hello!';
});

$app->run();
