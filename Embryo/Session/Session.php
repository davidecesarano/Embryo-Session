<?php 

    namespace Embryo\Session;

    class Session 
    {
        private $id;
        private $options = [];
        private $data = [];

        public function __construct(string $id, array $options)
        {
            if (session_status() === PHP_SESSION_DISABLED) {
                throw new \RuntimeException('PHP sessions are disabled');
            }
            
            if (session_status() === PHP_SESSION_ACTIVE) {
                throw new \RuntimeException('Failed to start the session: already started by PHP.');
            }

            session_id($id);
            session_start($options);

            $this->id      = $id;
            $this->options = $options;
            $this->data    = $_SESSION;
        }

        public function save($name): void
        {
            if ((session_status() === PHP_SESSION_ACTIVE) && (session_name() === $name)) {
                $_SESSION = $this->data;
                session_write_close();
            }
        }

        public function id(): string
        {
            return $this->id;
        }

        public function get(string $name, $default = null)
        {
            return $this->data[$name];
        }

        public function set($name, $value)
        {
            $this->data[$name] = $value;
        }

        public function all(): array
        {
            return $this->data;
        }

        public function has(string $name)
        {

        }
    }