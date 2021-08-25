<?php
namespace Small\Core;

use Small\Interfaces\MiddlewareInterface;
use Small\Interfaces\RequestHandlerInterface;
use Small\Interfaces\RequestInterface;
use Small\Interfaces\ResponseInterface;

class MiddlewareStack
{
    private $top;

    public function __construct(RequestHandlerInterface $kernel)
    {
        $this->seedMiddlewareStack($kernel);
    }

    public function seedMiddlewareStack(RequestHandlerInterface $kernel)
    {
        $this->top = $kernel;
    }

    public function handle(RequestInterface $request) : ResponseInterface
    {
        return $this->top->handle($request);
    }

    public function addMiddleware(MiddlewareInterface $middleware) : MiddlewareStack
    {
        $next = $this->top;

        $this->top = new class ($middleware, $next) implements RequestHandlerInterface {
            private $middleware;

            private $next;

            public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $handler)
            {
                $this->middleware = $middleware;
                $this->next = $handler;
            }

            public function handle(RequestInterface $request) : ResponseInterface
            {
                return $this->middleware->process($request, $this->next);
            }
        };

        return $this;
    }
}
