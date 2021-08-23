<?php
namespace Small\Interfaces;

interface StreamInterface
{
    public function __toString();
    
    public function getSize();
    
    public function write($string);

    public function read($length) : string;

    public function detach();

    public function eof() : bool;

    public function isSeekable() : bool;

    public function isReadable() : bool;

    public function isWritable() : bool;

    public function rewind() : void;

    public function getContents() : string;

    public function getMetadata($key = null);
}