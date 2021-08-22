<?php
    namespace Small\Core;

    use Small\Interfaces\StreamInterface;

    class StreamFactory
    {
        public function create(string $content = '') : StreamInterface
        {
            $resource = fopen('php://temp', 'rw+');

            if (!is_resource($resource)) {
                // TODO: throw exception
            }

            fwrite($resource, $content);
            rewind($resource);

            return $this->createFromResource($resource);
        }

        public function createFromResource($resource, StreamInterface $cache = null) : StreamInterface
        {
            if (!is_resource($resource)) {
                // TODO: Throw exception
            }

            return new Stream($resource, $cache);
        }
    }
    