<?php
    require_once __DIR__ . '/vendor/autoload.php';

    use Small\Core\Application;
    use Small\Interfaces\RequestInterface;
    use Small\Interfaces\ResponseInterface;

    $app = new Application();

    $app->get('/api', function (RequestInterface $request, ResponseInterface $response, $args) {
        $response->getBody()->write('Index: ');

        $queryParams = $request->getQueryParams();
        $username = $queryParams['username'] ?? 'Invitado';
        $edad = $queryParams['age'] ?? '?';

        $response->getBody()->write(" Bienvenido $username tienes $edad años");

        return $response;
    });

    $app->get('/api/contact/$id/$username', function (RequestInterface $request, ResponseInterface $response, $args) {
        $id = $request->getAttribute('id');
        $username = $request->getAttribute('username');
        $response->getBody()->write("Hello! I'm $username with id $id");
        return $response;
    });

    $app->run();