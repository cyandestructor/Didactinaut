<?php
    namespace Small\Core;

    use Small\Interfaces\StreamInterface;

    // TEMPORAL
    class Stream implements StreamInterface
    {
        private $stream;

        public function write($string)
        {
            $this->stream = $string;   
        }

        public function getSize()
        {
            return strlen($this->stream);
        }
        
        public function read($length) : string
        {
            return substr($this->stream, 0, $length);
        }
    }
    