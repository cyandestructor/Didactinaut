<?php
namespace Small\Middleware;

use Small\Interfaces\MiddlewareInterface;
use Small\Interfaces\RequestInterface;
use Small\Interfaces\ResponseInterface;
use Small\Interfaces\RequestHandlerInterface;

class BodyParserMiddleware implements MiddlewareInterface
{
    private $bodyParsers;

    public function __construct()
    {
        $this->registerDefaultBodyParsers();
    }

    public function process(RequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        if ($parsedBody === null || empty($parsedBody)) {
            $parsedBody = $this->parseBody($request);
            $request = $request->withParsedBody($parsedBody);
        }

        return $handler->handle($request);
    }

    public function registerBodyParser(string $mediaType, callable $callable) : self
    {
        $this->bodyParsers[$mediaType] = $callable;
        return $this;
    }

    private function registerDefaultBodyParsers()
    {
        $this->registerBodyParser('application/json', static function ($input) {
            $result = json_decode($input, true);

            if(!is_array($result)) {
                return null;
            }

            return $result;
        });

        $this->registerBodyParser('application/x-www-form-urlencoded', static function ($input) {
            parse_str($input, $data);
            return $data;
        });
    }

    private function getMediaType(RequestInterface $request) : ?string
    {
        $contentType = $request->getHeader('Content-Type')[0] ?? null;

        if (is_string($contentType) && trim($contentType) !== '') {
            $contentTypeParts = explode(';', $contentType);
            return strtolower(trim($contentTypeParts[0]));
        }

        return null;
    }

    private function parseBody(RequestInterface $request)
    {
        $mediaType = $this->getMediaType($request);
        if ($mediaType == null) {
            return null;
        }

        if (isset($this->bodyParsers[$mediaType])) {
            $body = (string)$request->getBody();
            $parsedBody = $this->bodyParsers[$mediaType]($body);

            return $parsedBody;
        }

        return null;
    }
}
