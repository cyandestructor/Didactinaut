<?php
    require_once __DIR__ . '/vendor/autoload.php';

    use Small\Core\Application;

    use Small\Middleware\BodyParserMiddleware;

    use Didactinaut\Controllers\UsersController;
    use Didactinaut\Factories\Database\MySQLDatabaseFactory;

    $databaseFactory = new MySQLDatabaseFactory('localhost', 'didactinaut_dev');

    $app = new Application();

    $app->addMiddleware(new BodyParserMiddleware());

    $app->post('/api/users/', [new UsersController($databaseFactory), 'postUser']);

    $app->run();