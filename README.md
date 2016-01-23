# PHP-DI integration with Slim

[![Build Status](https://travis-ci.org/PHP-DI/Slim-Bridge.svg?branch=master)](https://travis-ci.org/PHP-DI/Slim-Bridge)

## Installation

```
composer require php-di/slim-bridge
```

## Usage

Instead of using `Slim\App`, simply use `DI\Bridge\Slim\App`:

```php
<?php
require 'vendor/autoload.php';

$app = new \DI\Bridge\Slim\App;
```

You can then use the application [just like a classic Slim application](http://www.slimframework.com/):

```php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $response->getBody()->write("Hello!");
    return $response;
});

$app->run();
```

### Configuring PHP-DI

If you want to configure PHP-DI, simply extend the `DI\Bridge\Slim\App` class and override the `configureContainer()` method:

```php
class MyApp extends \DI\Bridge\Slim\App
{
    protected function configureContainer(ContainerBuilder $builder)
    {
        $builder->addDefinitions(__DIR__ . 'my-config-file.php');
    }
}

$app = new MyApp;
```

Or if you are using PHP 7 you can use anonymous classes:

```php
$app = new class() extends \DI\Bridge\Slim\App {
   protected function configureContainer(ContainerBuilder $builder)
   {
       $builder->addDefinitions(__DIR__ . 'my-config-file.php');
   }
};
```
