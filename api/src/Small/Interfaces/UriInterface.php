<?php
namespace Small\Interfaces;

interface UriInterface
{
    public function getScheme() : string;

    public function withScheme($scheme);

    public function getUserInfo();

    public function withUserInfo($user, $password = null);

    public function getHost() : string;

    public function withHost($host);

    public function getPath() : string;

    public function withPath($path);

    public function getPort() : ?int;

    public function withPort($port);

    public function getQuery() : string;

    public function withQuery($query);

    public function getFragment() : string;

    public function withFragment($fragment);
}