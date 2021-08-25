<?php
namespace Small\Interfaces;

interface MiddlewareInterface
{
    public function process(RequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface;
}