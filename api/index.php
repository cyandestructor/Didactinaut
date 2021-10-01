<?php
    require_once __DIR__ . '/vendor/autoload.php';

    use Small\Core\Application;

    use Small\Middleware\BodyParserMiddleware;

    use Didactinaut\Controllers\UsersController;
    use Didactinaut\Factories\Database\MySQLDatabaseFactory;

    $databaseFactory = new MySQLDatabaseFactory('localhost', 'didactinaut_dev');

    $app = new Application();

    $app->addMiddleware(new BodyParserMiddleware());

    // Users
    {
        $app->get('/api/users/', [new UsersController($databaseFactory), 'checkUserExists']);
        $app->get('/api/users/$id', [new UsersController($databaseFactory), 'getUnique']);
        $app->post('/api/users/', [new UsersController($databaseFactory), 'postUser']);
        $app->put('/api/users/$id', [new UsersController($databaseFactory), 'putUser']);
        $app->put('/api/users/$id/image', [new UsersController($databaseFactory), 'putUserImage']);
    }

    $app->run();