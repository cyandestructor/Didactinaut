<?php
namespace Small\Middleware;

use Small\Interfaces\MiddlewareInterface;
use Small\Interfaces\RequestInterface;
use Small\Interfaces\ResponseInterface;
use Small\Interfaces\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function process(RequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $this->startSession();

        return $handler->handle($request);
    }

    public function startSession()
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        session_start();
    }
}
