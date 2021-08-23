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
        
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $uriInfo = parse_url($url);
        $uriInfo['port'] = $_SERVER['SERVER_PORT'];
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

        $cacheResource = fopen('php://temp', 'wb+');
        $cache = $cacheResource ? new Stream($cacheResource) : null;

        $inputResource = fopen('php://input', 'r');

        $body = (new StreamFactory())->createFromResource($inputResource, $cache);
        
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
