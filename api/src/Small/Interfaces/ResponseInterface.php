<?php
    namespace Small\Interfaces;

    interface ResponseInterface
    {
        public function getStatusCode() : int;
        public function withStatus($code);
    }