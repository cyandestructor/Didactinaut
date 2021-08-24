<?php
declare(strict_types=1);

namespace Small\Core;

use Small\Interfaces\RequestInterface;
use Small\Interfaces\MiddlewareInterface;

class Application
{
    private Router $router;
    private MiddlewareStack $middlewareStack;

    public function __construct()
    {
        $this->router = new Router();
        $this->middlewareStack = new MiddlewareStack($this->router);
    }

    public function get(string $path, $handler)
    {
        $this->router->get($path, $handler);
    }

    public function post(string $path, $handler)
    {
        $this->router->post($path, $handler);
    }

    public function put(string $path, $handler)
    {
        $this->router->put($path, $handler);
    }

    public function delete(string $path, $handler)
    {
        $this->router->delete($path, $handler);
    }

    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middlewareStack->addMiddleware($middleware);
    }

    public function run(RequestInterface $request = null) : void
    {
        if (!$request) {
            $requestFactory = new RequestFactory();
            $request = $requestFactory->createFromGlobals();
        }

        $response = $this->middlewareStack->handle($request);
        
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }
}
