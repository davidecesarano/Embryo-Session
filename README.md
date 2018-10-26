# Embryo-Session
Middleware to start a php session using the request data and close it after return the response.

## Requirements
* PHP >= 7.1
* A [PSR-7](https://www.php-fig.org/psr/psr-7/) http message implementation (ex. [Embryo-Http](https://github.com/davidecesarano/embryo-http))
* A [PSR-15](https://www.php-fig.org/psr/psr-15/) http server request implementation (ex. [Embryo-Middleware](https://github.com/davidecesarano/embryo-middleware))

## Installation
Using Composer:
```
$ composer require davidecesarano/embryo-session
```

## Usage
Creates a middleware for setting session item:
```php
class TestSetSessionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute('session');
        $session->set('name', 'World');
        $response = $handler->handle($request);
        return $response->write('Hello '.$session->get('name').'</p><p><a href="test.php">Other Page</a></p>');
    }
}
```
Creates another middleware for getting session item:
```php
class TestGetSessionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute('session');
        $response = $handler->handle($request);
        return $response->write('Hello '.$session->get('name').'</p>');
    }
}
```
Adds middleware to dispatcher:
```php
$middleware = new MiddlewareDispatcher;
$middleware->add((new SessionMiddleware)->setOptions([
    'use_cookies'      => false,
    'use_only_cookies' => true
]));
$middleware->add(TestSetSessionMiddleware::class);
$middleware->add(TestGetSessionMiddleware::class);
$response = $middleware->dispatch($request, $response);
```

## Example
You may quickly test this using the built-in PHP server:
```
$ php -S localhost:8000
```
Going to http://localhost:8000/example/ will now display "Hello world".

## Options
### `setName(string $name)`
The session name. If it's not provided, use the php's default.

### `seOptions(string $name)`
Array of options passed to `session_start()`.