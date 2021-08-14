<?php
    namespace Small\Core;

    use Small\Http\Response;
    use Small\Interfaces\ResponseInterface;

    class ResponseFactory
    {
        public function create() : ResponseInterface
        {
            $body = new Stream();
            return new Response(Response::$STATUS_CODE_OK, [], $body);
        }
    }
    