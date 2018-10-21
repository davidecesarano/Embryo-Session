<?php 

    namespace Embryo\Session\Middleware;

    use Embryo\Session\Session;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;

    class SessionMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            $cookies  = $request->getCookieParams();
            $id       = $cookies[session_name()] ?? bin2hex(random_bytes(16));
            $session  = new Session($id, [
                'use_cookies'      => false,
                'use_only_cookies' => true
            ]);
            $session->save(session_name());

            $request  = $request->withAttribute('session', $session);
            $response = $handler->handle($request);
            
            return isset($cookies[session_name()]) ? $response : $response->withHeader(
                'Set-Cookie',
                sprintf("%s=%s; path=%s", session_name(), $id, ini_get('session.cookie_path'))
            );
        }
    }