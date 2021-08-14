<?php
    namespace Small\Core;

    use Small\Interfaces\UriInterface;

    class Uri implements UriInterface
    {
        private $scheme;

        private $user;
        
        private $password;
        
        private $host;
        
        private $port;
        
        private $path;
        
        private $query;
        
        private $fragment;

        public function __construct(
            string $scheme,
            string $host,
            ?int $port = null,
            string $path = '/',
            string $query = '',
            string $fragment = '',
            string $user = '',
            string $password = '',
        )
        {
            $this->scheme = $scheme;
            $this->host = $host;
            $this->port = $port;
            $this->path = $path;
            $this->query = $query;
            $this->fragment = $fragment;
            $this->user = $user;
            $this->password = $password;
        }

        public function getScheme() : string
        {
            return $this->scheme;
        }

        public function withScheme($scheme)
        {
            $clone = clone $this;
            $clone->scheme = $scheme;
            
            return $clone;
        }

        public function getUserInfo()
        {
            $info = $this->user;

            if(isset($this->password) && $this->password !== '')
            {
                $info .= ":$this->password";
            }

            return $info;
        }

        public function withUserInfo($user, $password = null)
        {
            $clone = clone $this;
            $clone->user = $user;

            if ($password && $password !== '') {
                $clone->password = $password;
            }
            
            return $clone;
        }

        public function getHost() : string
        {
            return $this->host;
        }

        public function withHost($host)
        {
            $clone = clone $this;
            $clone->host = $host;
            
            return $clone;
        }

        public function getAuthority(): string
        {
            $userInfo = $this->getUserInfo();
            $host = $this->getHost();
            $port = $this->getPort();

            return ($userInfo !== '' ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');
        }

        public function getPath() : string
        {
            return $this->path;
        }

        public function withPath($path)
        {
            $clone = clone $this;
            $clone->path = $path;

            return $clone;
        }

        public function getPort() : ?int
        {
            return $this->port ?? null;
        }

        public function withPort($port)
        {
            $clone = clone $this;
            $clone->port = $port;
            
            return $clone;
        }

        public function getQuery() : string
        {
            return $this->query;
        }

        public function withQuery($query)
        {
            $clone = clone $this;
            $clone->query = $query;
            
            return $clone;
        }

        public function getFragment() : string
        {
            return $this->fragment;
        }

        public function withFragment($fragment)
        {
            $clone = clone $this;
            $clone->fragment = $fragment;
            
            return $clone;
        }

        public function __toString(): string
        {
            $scheme = $this->getScheme();
            $authority = $this->getAuthority();
            $path = $this->getPath();
            $query = $this->getQuery();
            $fragment = $this->getFragment();

            $path = '/' . ltrim($path, '/');

            return ($scheme !== '' ? $scheme . ':' : '')
                . ($authority !== '' ? '//' . $authority : '')
                . $path
                . ($query !== '' ? '?' . $query : '')
                . ($fragment !== '' ? '#' . $fragment : '');
        }
    }
    