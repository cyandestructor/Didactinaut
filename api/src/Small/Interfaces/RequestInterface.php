<?php
    namespace Small\Interfaces;

    interface RequestInterface extends MessageInterface
    {
        public function getMethod() : string;
        public function withMethod($method);

        public function getUri() : UriInterface;
        public function withUri(UriInterface $uri);
    }