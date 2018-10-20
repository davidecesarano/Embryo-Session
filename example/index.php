<?php 

    require __DIR__ . '/../vendor/autoload.php';
    
    use Embryo\Http\Server\MiddlewareDispatcher;
    use Embryo\Http\Factory\{ServerRequestFactory, ResponseFactory};
    use Embryo\Session\Middleware\SessionMiddleware;

    $request = (new ServerRequestFactory)->createServerRequestFromServer();
    $response = (new ResponseFactory)->createResponse(200);

    $middleware = new MiddlewareDispatcher;
    $middleware->add(SessionMiddleware::class);
    $response = $middleware->dispatch($request, $response);

    echo '<pre>';
        print_r($response->getHeaders());
    echo '</pre>';