<?php
    require_once __DIR__ . '/vendor/autoload.php';

    use Small\Core\Application;
    use Small\Interfaces\RequestInterface;
    use Small\Interfaces\ResponseInterface;

    $app = new Application();

    $app->get('/', function (RequestInterface $request, ResponseInterface $response) {
        $response->getBody()->write('Index');
        return $response;
    });

    $app->get('/contact', function (RequestInterface $request, ResponseInterface $response) {
        $response->getBody()->write('Contact');
        return $response;
    });

    $app->run();