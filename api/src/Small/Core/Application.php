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
            $filteredPath = $this->filterPath($path);

            $this->handlers[$method . $filteredPath] = [
                'path' => $filteredPath,
                'method' => $method,
                'handler' => $handler
            ];
        }

        private function filterPath(string $path) : string
        {
            if(substr($path, -1) === '/')
            {
                return substr($path, 0, -1);
            }

            return $path;
        }

        private function getParameters(string $route, string $requestUrl)
        {
            $requestUrl = filter_var($requestUrl, FILTER_SANITIZE_URL);
            $requestUrl = rtrim($requestUrl, '/');
            $requestUrl = strtok($requestUrl, '?');
            $routeParts = explode('/', $route);
            $requestUrlParts = explode('/', $requestUrl);
            array_shift($routeParts);
            array_shift($requestUrlParts);

            if( $routeParts[0] == '' && count($requestUrlParts) == 0 )
            {
                return [];
            }

            if( count($routeParts) != count($requestUrlParts) )
            {
                return null;
            }

            $parameters = [];
            for( $__i__ = 0; $__i__ < count($routeParts); $__i__++ ){
                $routerPart = $routeParts[$__i__];
                if( preg_match("/^[$]/", $routerPart) )
                {
                    $routerPart = ltrim($routerPart, '$');
                    $parameters[$routerPart] = $requestUrlParts[$__i__];
                }
                else if( $routeParts[$__i__] != $requestUrlParts[$__i__] )
                {
                    return null;
                }
            }

            return $parameters;
        }

        public function run(RequestInterface $request = null) : void
        {
            if (!$request) {
                $requestFactory = new RequestFactory();
                $request = $requestFactory->createFromGlobals();
            }

            $requestPath = $this->filterPath($request->getUri()->getPath());
            $requestMethod = $request->getMethod();
            
            $callback = null;
            $parameters = null;
            foreach ($this->handlers as $handler) {
                $parameters = $this->getParameters($handler['path'], $requestPath);
                if ($parameters !== null && $handler['method'] === $requestMethod) {
                    $callback = $handler['handler'];
                    break;
                }
            }

            if (!$callback) {
                // TODO: Call 404
                return;
            }

            $request = $request->withAttributes($parameters);
            
            $responseFactory = new ResponseFactory();
            $response = $responseFactory->create();
            $response = call_user_func($callback, $request, $response, $parameters);
            
            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
    