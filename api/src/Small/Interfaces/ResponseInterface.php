<?php
namespace Small\Interfaces;

interface ResponseInterface extends MessageInterface
{
    public function getStatusCode() : int;
    public function withStatus($code);
}