<?php
declare(strict_types=1);

namespace Small\Http;

use Small\Interfaces\StreamInterface;
use Small\Interfaces\RequestInterface;
use Small\Interfaces\UriInterface;

class Request extends Message implements RequestInterface
{
    private string $method;

    private array $attributes;

    private $queryParams;
    
    private UriInterface $uri;

    public function __construct(
        string $method,
        array $headers = [],
        StreamInterface $body = null,
        UriInterface $uri = null)
    {
        parent::__construct($headers, $body);
        $this->method = $method;
        $this->uri = $uri;
        $this->attributes = [];
        $this->queryParams = null;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    public function getUri() : UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri)
    {
        $clone = clone $this;
        $clone->uri = $uri;

        return $clone;
    }

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function withAttributes(array $attributes)
    {
        $clone = clone $this;
        $clone->attributes = $attributes;

        return $clone;
    }

    public function withAddedAttributes(array $attributes)
    {
        $clone = clone $this;
        $clone->attributes = array_merge($clone->attributes, $attributes);

        return $clone;
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        
        return $clone;
    }

    public function getQueryParams() : array
    {
        if (is_array($this->queryParams)) {
            return $this->queryParams;
        }

        if ($this->uri === null) {
            return [];
        }

        parse_str($this->uri->getQuery(), $this->queryParams);

        return $this->queryParams;
    }

    public function withQueryParams(array $queryParams)
    {
        $clone = clone $this;
        $clone->queryParams = $queryParams;

        return $clone;
    }
}
