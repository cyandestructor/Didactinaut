<?php
    namespace Small\Interfaces;

    interface RequestInterface extends MessageInterface
    {
        public function getMethod() : string;
        public function withMethod($method);

        public function getUri() : UriInterface;
        public function withUri(UriInterface $uri);

        public function getAttributes() : array;
        public function withAttributes(array $attributes);
        public function getAttribute($name, $default = null);
        public function withAttribute($name, $value);

        public function getQueryParams() : array;
        public function withQueryParams(array $queryParams);
    }