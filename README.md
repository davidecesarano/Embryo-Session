# Embryo Session
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

## Collection
### Retrieving data
You may retrieve an item from the session and you may also pass a default value as the second argument to the `get` method:
```php
$session->get('key', 'default');
```

### Retrieving all session data
If you would like to retrieve all the data in the session, you may use the `all` method:
```php
$session->all();
```

### Determining if an item exists in the session
To determine if an item is present in the session, you may use the `has` method. The has method returns `true` if the item is present and is not `null`:
```php
if ($session->has('key')) {
    //...
}
```

### Storing data
The `set` method may be used to set a new value onto a session:
```php
$session->set('name', 'value');
```

### Flash data
You may wish to store items in the session only for the next request using the `flash` method:
```php
$session->flash('name', 'value');
```

### Deleting data
The `remove` method will remove a piece of data from the session. If you would like the remove all data from the session, you may use the `clear` method:
```php
$session->remove('name');
$session->clear();
```

### Regenerating the session id
If you need the regenerating session id, you may user `regenerate` method:
```php
$session->regenerate();
```
