<?php
    declare(strict_types=1);

    namespace Small\Core;

    use Small\Interfaces\ResponseInterface;

    class ResponseEmitter
    {
        private $responseChunkSize;

        public function __construct(int $responseChunkSize = 4096)
        {
            $this->responseChunkSize = $responseChunkSize;
        }

        public function emit(ResponseInterface $response)
        {
            $this->emitStatusLine($response);
            $this->emitHeaders($response);
            $this->emitBody($response);
        }

        private function emitHeaders(ResponseInterface $response)
        {
            foreach ($response->getHeaders() as $name => $value) {
                $header = "$name: $value";
                header($header);
            }
        }

        private function emitBody(ResponseInterface $response)
        {
            $body = $response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            $amountToRead = (int) $response->getHeaderLine('Content-Length');
            if (!$amountToRead) {
                $amountToRead = $body->getSize();
            }

            if ($amountToRead) {
                while ($amountToRead > 0 && !$body->eof()) {
                    $length = min($this->responseChunkSize, $amountToRead);
                    $data = $body->read($length);
                    echo $data;

                    $amountToRead -= strlen($data);

                    if (connection_status() !== CONNECTION_NORMAL) {
                        break;
                    }
                }
            } else {
                while (!$body->eof()) {
                    echo $body->read($this->responseChunkSize);
                    if (connection_status() !== CONNECTION_NORMAL) {
                        break;
                    }
                }
            }
        }

        private function emitStatusLine(ResponseInterface $response)
        {
            // TEMPORAL
            http_response_code($response->getStatusCode());
        }
    }
    