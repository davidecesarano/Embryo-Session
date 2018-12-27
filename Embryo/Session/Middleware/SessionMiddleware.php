<?php 

    /**
     * SessionMiddleware
     * 
     * PSR-15 Middleware to start a PHP session using the request data and 
     * close it after return the response.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-session
     */

    namespace Embryo\Session\Middleware;

    use Embryo\Session\Session;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;

    class SessionMiddleware implements MiddlewareInterface
    {
        /**
         * @var Session $session
         */
        private $session;

        /**
         * @var string $name
         */
        private $name = 'PHPSESSID';
        
        /**
         * @var array $options
         */
        private $options = [];

        /**
         * @var string $sessionRequestAttribute
         */
        private $sessionRequestAttribute = 'session';

        /**
         * Set session.
         *
         * @param Session $session
         * @return self
         */
        public function setSession(Session $session): self
        {
            $this->session = $session;
            return $this;
        }

        /**
         * Sets session name.
         *
         * @param string $name
         * @return self
         */
        public function setName(string $name): self
        {
            $this->name = $name;
            return $this;
        }

        /**
         * Sets session options.
         *
         * @param array $options
         * @return self
         */
        public function setOptions(array $options): self 
        {
            $this->options = $options;
            return $this;
        }

        /**
         * Set session request attribute.
         *
         * @param string $name
         * @return self
         */
        public function setSessionRequestAttribute(string $name): self
        {
            $this->sessionRequestAttribute = $name;
            return $this;
        }

        /**
         * Process a server request and return a response.
         *
         * @param ServerRequestInterface $request
         * @param RequestHandlerInterface $handler
         * @return ResponseInterface
         */
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            $cookies  = $request->getCookieParams();
            $name     = $this->name ?: session_name();
            $id       = $cookies[$name] ?? bin2hex(random_bytes(16));
            $options  = $this->options;
            $session  = $this->session->start($id, $name, $options);
            
            $request  = $request->withAttribute($this->sessionRequestAttribute, $session);
            $response = $handler->handle($request);
            $session->save();
            
            return isset($cookies[$name]) ? $response : $response->withHeader(
                'Set-Cookie',
                sprintf(
                    "%s=%s; path=%s", 
                    $name, 
                    $id, 
                    ini_get('session.cookie_path')
                )
            );
        }
    }