<?php
    namespace Small\Interfaces;

    interface StreamInterface
    {
        public function getSize();
        
        public function write($string);

        public function read($length) : string;
    }