<?php 

    /**
     * SessionCollectionTrait
     * 
     * Collection methods for PHP Session. 
     *
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-session
     */

    namespace Embryo\Session;

    trait SessionCollectionTrait 
    {
        /**
         * Get session id.
         *
         * @return string
         */
        public function id(): string
        {
            return $this->id;
        }

        /**
         * Regenerate session id.
         *
         * @return void
         */
        public function regenerate(): void
        {
            session_regenerate_id(true);
        }

        /**
         * Get session name.
         *
         * @return string
         */
        public function name(): string 
        {
            return $this->name;
        }

        /**
         * Get item session from name.
         *
         * @param string $name
         * @param mixed $default
         * @return mixed
         */
        public function get(string $name, $default = null)
        {
            return $this->has($name) ? $this->data[$name] : $default;
        }

        /**
         * Get all items in session.
         *
         * @return array
         */
        public function all(): array
        {
            return $this->data;
        }

        /**
         * Get session keys.
         *
         * @return array
         */
        public function keys(): array
        {
            return array_keys($this->data);
        }

        /**
         * Store data in session.
         *
         * @param string $name
         * @param mixed $value
         * @return void
         */
        public function set(string $name, $value): void
        {
            $this->data[$name] = $value;
        }
        
        /**
         * Store items in the session only for 
         * the next request.
         *
         * @param string $name
         * @param mixed $value
         * @return void
         */
        public function flash(string $name, $value): void
        {
            if ($this->has($name)) {
                $this->remove($name);
            } else {
                $this->set($name, $value);
            }
        }

        /**
         * Return true or false if session has or 
         * not item name.
         *
         * @param string $name
         * @return bool
         */
        public function has(string $name): bool
        {
            return array_key_exists($name, $this->data);
        }

        /**
         * Remove session item name.
         *
         * @param string $name
         * @return void
         */
        public function remove(string $name)
        {
            unset($this->data[$name]);
        }

        /**
         * Clear session.
         *
         * @return void
         */
        public function clear(): void
        {
            $this->data = [];
        }
    }