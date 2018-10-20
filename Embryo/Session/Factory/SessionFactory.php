<?php 

    namespace Embryo\Session\Factory;

    use Embryo\Session\PhpSession;

    class SessionFactory
    {
        public function createSession(string $name)
        {
            return new PhpSession($name, $_SESSION);
        }
    }