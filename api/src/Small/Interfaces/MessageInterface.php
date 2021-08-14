<?php
    namespace Small\Interfaces;

    interface MessageInterface
    {
        public function getHeaders() : array;
        public function hasHeader($name) : bool;
        public function getHeader($name) : array;
        public function getHeaderLine($name) : string;
        public function withHeader($name, $value);

        public function getBody() : StreamInterface;
        public function withBody(StreamInterface $body);
    }