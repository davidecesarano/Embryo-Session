<?php 

    namespace Embryo\Session;

    class Session 
    {
        private $id;
        private $data;

        public function __construct(string $id, array $data)
        {
            $this->id = $id;
            $this->data;
        }

        public function get(string $name, $default = null)
        {

        }

        public function set()
        {

        }

        public function all()
        {

        }

        public function has(string $name)
        {

        }
    }