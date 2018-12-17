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
         * @var array $array
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
         * @throws RuntimeException
         */
        public function __construct(string $id, string $name = 'PHPSESSID', array $options = [])
        {
            if ($this->disabled()) {
                throw new \RuntimeException('PHP sessions are disabled');
            }
            
            if ($this->active()) {
                throw new \RuntimeException('Failed to start the session: already started by PHP.');
            }

            session_name($name);
            session_id($id);
            session_start($options);

            $this->name    = $name;
            $this->id      = $id;
            $this->options = $options;
            $this->data    = $_SESSION;
        }

        /**
         * Save and close session.
         *
         * @return void
         */
        public function save(): void
        {
            if ($this->active() && session_name() == $this->name()) {
                $_SESSION = $this->all();
                session_write_close();
            }
        }
        
        /**
         * Checks if sessions are disabled.
         *
         * @return bool
         */
        private function disabled(): bool
        {
            return session_status() === PHP_SESSION_DISABLED;
        }

        /**
         * Checks if sessions are enabled, 
         * and one exists.
         *
         * @return bool
         */
        private function active(): bool
        {
            return session_status() === PHP_SESSION_ACTIVE;
        }
    }