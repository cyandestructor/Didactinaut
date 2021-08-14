<?php
    namespace Small\Core;

    use Small\Interfaces\RequestInterface;
    use Small\Http\Request;
    use Small\Core\Uri;

    class RequestFactory
    {
        public function createFromGlobals() : RequestInterface
        {
            $method = $_SERVER['REQUEST_METHOD'];

            $headers = $this->processHeaders();
            
            $uriStr = $_SERVER['REQUEST_URI'];
            $uriInfo = parse_url($uriStr);
            $uri = new Uri(
                $uriInfo['scheme'],
                $uriInfo['host'],
                $uriInfo['port'],
                $uriInfo['path'] ?? '/',
                $uriInfo['query'] ?? '',
                $uriInfo['fragment'] ?? '',
                $uriInfo['user'] ?? '',
                $uriInfo['password'] ?? ''
            );

            $body = new Stream();
            
            $request = new Request(
                $method,
                $headers,
                $body,
                $uri
            );

            return $request;
        }

        private function processHeaders()
        {
            $headers = [];

            foreach ($_SERVER as $name => $value)
            {
                if (substr($name, 0, 5) == 'HTTP_')
                {
                    $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $headers[$name] = $value;
                } else if ($name == "CONTENT_TYPE") {
                    $headers["Content-Type"] = $value;
                } else if ($name == "CONTENT_LENGTH") {
                    $headers["Content-Length"] = $value;
                }
            }

            return $headers;
        }
    }
    