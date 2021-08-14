<?php
    declare(strict_types=1);

    namespace Small\Http;

    use Small\Interfaces\RequestInterface;
    use Small\Interfaces\UriInterface;

    class Request extends Message implements RequestInterface
    {
        private string $method;
        
        private UriInterface $uri;

        public function getMethod() : string
        {
            return $this->method;
        }

        public function withMethod($method)
        {
            $clone = clone $this;
            $clone->method = $method;

            return $clone;
        }

        public function getUri() : UriInterface
        {
            return $this->uri;
        }

        public function withUri(UriInterface $uri)
        {
            $clone = clone $this;
            $clone->uri = $uri;

            return $clone;
        }
    }
    