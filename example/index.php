<?php 

    require __DIR__ . '/../vendor/autoload.php';
    
    use Embryo\Http\Server\MiddlewareDispatcher;
    use Embryo\Http\Factory\{ServerRequestFactory, ResponseFactory};
    use Embryo\Session\Middleware\SessionMiddleware;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;

    $request = (new ServerRequestFactory)->createServerRequestFromServer();
    $response = (new ResponseFactory)->createResponse(200);

    class TestSetSessionMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            $session = $request->getAttribute('session');
            $session->set('name', 'World');
            $response = $handler->handle($request);
            return $response->write('Hello '.$session->get('name'));
        }
    }

    $middleware = new MiddlewareDispatcher;
    $middleware->add(SessionMiddleware::class);
    $middleware->add(TestSetSessionMiddleware::class);
    $response = $middleware->dispatch($request, $response);