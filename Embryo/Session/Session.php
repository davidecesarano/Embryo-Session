<?php 

    /**
     * Session
     * 
     * Class to start PHP Session.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-session
     */

    namespace Embryo\Session;

    use Embryo\Session\Traits\SessionCollectionTrait;

    class Session 
    {
        use SessionCollectionTrait;

        /**
         * @var string $id
         */
        protected $id;
        
        /**
         * @var string $name
         */
        protected $name;

        /**
         * @var array $options
         */
        protected $options = [];

        /**
         * @var array $data
         */
        protected $data = [];

        /**
         * Starts session with specific
         * id, name and options.
         *
         * @param string $id
         * @param string $name
         * @param array $options
         * @throws \RuntimeException
         */
        public function start(string $id, string $name = 'PHPSESSID', array $options = []): self
        {
            if ($this->isDisabled()) {
                throw new \RuntimeException('PHP sessions are disabled');
            }
            
            if ($this->isActive()) {
                throw new \RuntimeException('Failed to start the session: already started by PHP.');
            }

            session_name($name);
            session_id($id);
            session_start($options);

            $this->name    = $name;
            $this->id      = $id;
            $this->options = $options;
            $this->data    = $_SESSION;
            return $this;
        }

        /**
         * Returns an array with the current 
         * session cookie information.
         * 
         * @return array
         */
        public function getCookieParams(): array
        {
            return session_get_cookie_params();
        }

        /**
         * Save and close session.
         *
         * @return void
         */
        public function save(): void
        {
            if ($this->isActive() && session_name() == $this->getName()) {
                $_SESSION = $this->all();
                session_write_close();
            }
        }
        
        /**
         * Checks if sessions are disabled.
         *
         * @return bool
         */
        private function isDisabled(): bool
        {
            return session_status() === PHP_SESSION_DISABLED;
        }

        /**
         * Checks if sessions are enabled, 
         * and one exists.
         *
         * @return bool
         */
        private function isActive(): bool
        {
            return session_status() === PHP_SESSION_ACTIVE;
        }

        /**
         * Generate a random id.
         * 
         * @param int $length
         * @return string
         */
        public static function generateId(int $length = 64): string
        {
            $id = '';
            $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'), ['-', ',']);
            $max = count($characters) - 1;
            for ($i = 0; $i < $length; $i++) {
                $rand = mt_rand(0, $max);
                $id .= $characters[$rand];
            }
            return $id;
        }
    }