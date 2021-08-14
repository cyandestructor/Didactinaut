<?php
    declare(strict_types=1);

    namespace Small\Core;

    use Small\Interfaces\ResponseInterface;

    class ResponseEmitter
    {
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
            // TEMPORAL
            $length = $response->getBody()->getSize();
            echo $response->getBody()->read($length);
        }

        private function emitStatusLine(ResponseInterface $response)
        {
            // TEMPORAL
            http_response_code($response->getStatusCode());
        }
    }
    