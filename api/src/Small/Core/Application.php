<?php
    declare(strict_types=1);
    
    namespace Small\Core;

    use Small\Interfaces\RequestInterface;

    class Application
    {
        private array $handlers = [];

        public function __construct()
        {
            
        }

        public function get(string $path, $handler)
        {
            $this->addHandler('GET', $path, $handler);
        }

        public function post(string $path, $handler)
        {
            $this->addHandler('GET', $path, $handler);
        }

        public function put(string $path, $handler)
        {
            $this->addHandler('GET', $path, $handler);
        }

        public function delete(string $path, $handler)
        {
            $this->addHandler('GET', $path, $handler);
        }

        private function addHandler(string $method, string $path, $handler)
        {
            $this->handlers[$method . $path] = [
                'path' => $path,
                'method' => $method,
                'handler' => $handler
            ];
        }

        public function run(RequestInterface $request = null) : void
        {
            if (!$request) {
                $requestFactory = new RequestFactory();
                $request = $requestFactory->createFromGlobals();
            }

            $requestPath = $request->getUri()->getPath();
            $requestMethod = $request->getMethod();
            
            $callback = null;
            foreach ($this->handlers as $handler) {
                if ($handler['path'] === $requestPath && $handler['method'] === $requestMethod) {
                    $callback = $handler['handler'];
                    break;
                }
            }

            if (!$callback) {
                // TODO: Call 404
                return;
            }

            $responseFactory = new ResponseFactory();
            $response = $responseFactory->create();
            $response = call_user_func($callback, $request, $response);
            
            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
    