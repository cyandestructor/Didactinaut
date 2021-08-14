<?php
    namespace Small\Http;

    use Small\Interfaces\ResponseInterface;

    class Response extends Message implements ResponseInterface
    {
        private $statusCode;

        public function getStatusCode() : int
        {
            return $this->statusCode;
        }

        public function withStatus($code)
        {
            $clone = clone $this;
            $clone->statusCode = $code;

            return $clone;
        }
    }
    