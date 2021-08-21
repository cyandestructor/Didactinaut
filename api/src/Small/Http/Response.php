<?php
    namespace Small\Http;

    use Small\Core\StreamFactory;
    use Small\Interfaces\ResponseInterface;
    use Small\Interfaces\StreamInterface;

    class Response extends Message implements ResponseInterface
    {
        private $statusCode;

        public static $STATUS_CODE_OK = 200;

        public function __construct(int $statusCode, array $headers = [], StreamInterface $body = null)
        {
            parent::__construct($headers, $body ?? (new StreamFactory())->create());
            $this->statusCode = $statusCode;
        }

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
    