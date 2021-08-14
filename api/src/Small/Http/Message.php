<?php
    namespace Small\Http;

    use Small\Interfaces\MessageInterface;
    use Small\Interfaces\StreamInterface;

    class Message implements MessageInterface
    {
        protected $body;
        protected array $headers = [];

        public function getHeaders() : array
        {
            return $this->headers;
        }

        public function hasHeader($name) : bool
        {
            return array_key_exists($name, $this->headers);
        }

        public function getHeader($name) : array
        {
            return $this->headers[$name];
        }
        
        public function getHeaderLine($name) : string
        {
            $values = $this->headers[$name];
            return implode(',', $values);
        }

        public function withHeader($name, $value)
        {
            $clone = clone $this;
            $clone->headers[$name] = [$value];

            return $clone;
        }

        public function getBody() : StreamInterface
        {
            return $this->body;
        }

        public function withBody(StreamInterface $body)
        {
            $clone = clone $this;
            $clone->body = $body;
            
            return $clone;
        }
    }
    