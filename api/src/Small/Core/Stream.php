<?php
    namespace Small\Core;

    use Small\Interfaces\StreamInterface;

    // TEMPORAL
    class Stream implements StreamInterface
    {
        private $stream;

        // private $finished;

        private $size;

        private $writable;

        private $readable;

        private $seekable;

        public function __construct($stream)
        {
            $this->attach($stream);
        }

        public function write($string)
        {
            $written = false;

            if ($this->isWritable() && $this->stream) {
                $written = fwrite($this->stream, $string);
            }

            if ($written !== false) {
                $this->size = null;
                return $written;
            }

            // TODO: Throw exception
        }

        public function getSize()
        {
            if ($this->stream && !$this->size) {
                $stats = fstat($this->stream);
    
                if ($stats) {
                    $this->size = isset($stats['size']) ? $stats['size'] : null;
                }
            }
    
            return $this->size;
        }
        
        public function read($length) : string
        {
            $data = false;

            if ($this->isReadable() && $this->stream) {
                $data = fread($this->stream, $length);
            }

            if (is_string($data)) {
                if ($this->eof()) {
                    // $this->finished = true;
                }

                return $data;
            }

            // TODO: Throw exception
        }

        private function attach($stream)
        {
            if ($this->stream) {
                $this->detach();
            }

            $this->stream = $stream;
        }

        public function detach()
        {
            $oldResource = $this->stream;
            $this->stream = null;
            $this->meta = null;
            $this->readable = null;
            $this->writable = null;
            $this->size = null;
            $this->finished = false;
            $this->seekable = null;
            // $this->isPipe = null;
            // $this->cache = null;

            return $oldResource;
        }

        public function eof() : bool
        {
            return $this->stream ? feof($this->stream) : true;
        }

        public function isSeekable() : bool
        {
            if ($this->seekable === null) {
                $this->seekable = false;
    
                if ($this->stream) {
                    $this->seekable = $this->getMetadata('seekable');
                }
            }
    
            return $this->seekable;
        }

        public function isReadable() : bool
        {
            if ($this->readable === null) {
                $this->readable = false;
    
                if ($this->stream) {
                    $mode = $this->getMetadata('mode');

                    if (strstr($mode, 'r') !== false || strstr($mode, '+') !== false) {
                        $this->readable = true;
                    }
                }
            }

            return $this->readable;
        }

        public function isWritable() : bool
        {
            if ($this->writable === null) {
                $this->writable = false;
    
                if ($this->stream) {
                    $mode = $this->getMetadata('mode');
    
                    if (strstr($mode, 'w') !== false || strstr($mode, '+') !== false) {
                        $this->writable = true;
                    }
                }
            }
    
            return $this->writable;
        }

        public function rewind() : void
        {
            if (!($this->stream && rewind($this->stream))) {
                // TODO: Throw exception
            }
        }

        public function getContents() : string
        {
            $contents = false;
            
            if ($this->stream) {
                $contents = stream_get_contents($this->stream);
            }

            if (is_string($contents)) {
                if ($this->eof()) {
                    $this->finished = true;
                }
                return $contents;
            }

            // TODO: throw exception
        }

        public function getMetadata($key = null)
        {
            if (!$this->stream) {
                return null;
            }
    
            $this->meta = stream_get_meta_data($this->stream);
    
            if (!$key) {
                return $this->meta;
            }
    
            return isset($this->meta[$key]) ? $this->meta[$key] : null;
        }

        public function __toString()
        {
            if (!$this->stream) {
                return '';
            }
            if ($this->isSeekable()) {
                $this->rewind();
            }
            return $this->getContents();
        }
    }
    