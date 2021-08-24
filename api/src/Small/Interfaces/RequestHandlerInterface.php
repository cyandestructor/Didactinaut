<?php
namespace Small\Interfaces;

interface RequestHandlerInterface
{
    public function handle(RequestInterface $request) : ResponseInterface;
}