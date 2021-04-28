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
            $cookies   = $request->getCookieParams();
            $name      = $this->name;
            $id_cookie = isset($cookies[$name]) ? $cookies[$name] : false; 
            $id        = !$id_cookie ? Session::generateId() : $id_cookie;
            $options   = $this->options;
            $session   = (new Session)->start($id, $name, $options);
            $params    = $session->getCookieParams();

            $request  = $request->withAttribute($this->sessionRequestAttribute, $session);
            $response = $handler->handle($request);
            $session->save();
            
            if (!$id_cookie) {
                return $this->writeSessionCookie($response, $name, $id, $params);
            }
            return $response;
        }

        private function writeSessionCookie(ResponseInterface $response, string $name, string $id, array $params): ResponseInterface
        {
            $cookie = $name.'='.$id;

            if (isset($params['lifetime'])) {
                $expires = gmdate('D, d M Y H:i:s T', time() + $params['lifetime']);
                $cookie .= "; expires={$expires}; max-age={$params['lifetime']}";
            }
    
            if (isset($params['domain'])) {
                $cookie .= "; domain={$params['domain']}";
            }
    
            if (isset($params['path'])) {
                $cookie .= "; path={$params['path']}";
            }
    
            if (isset($params['secure'])) {
                $cookie .= '; secure';
            }

            if (isset($params['samesite'])) {
                $cookie .= "; samesite={$params['samesite']}";
            }
    
            if (isset($params['httponly'])) {
                $cookie .= '; httponly';
            }

            return $response->withAddedHeader('Set-Cookie', $cookie);
        }
    }