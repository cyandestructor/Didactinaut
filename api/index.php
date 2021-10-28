<?php
require_once __DIR__ . '/vendor/autoload.php';

use Small\Core\Application;

use Small\Middleware\BodyParserMiddleware;
use Small\Middleware\SessionMiddleware;

use Didactinaut\Controllers\UsersController;
use Didactinaut\Controllers\ImagesController;
use Didactinaut\Controllers\SessionController;
use Didactinaut\Controllers\CategoryController;
use Didactinaut\Factories\Database\MySQLDatabaseFactory;

$databaseFactory = new MySQLDatabaseFactory('localhost', 'didactinaut_dev');

$app = new Application();

$app->addMiddleware(new SessionMiddleware());
$app->addMiddleware(new BodyParserMiddleware());

// Users
{
    $app->get('/api/users/', [new UsersController($databaseFactory), 'checkUserExists']);
    $app->get('/api/users/$id', [new UsersController($databaseFactory), 'getUnique']);
    $app->post('/api/users/', [new UsersController($databaseFactory), 'postUser']);
    $app->put('/api/users/$id', [new UsersController($databaseFactory), 'putUser']);
    $app->put('/api/users/$id/image', [new UsersController($databaseFactory), 'putUserImage']);
}

// Categories
{
    $app->get('/api/categories/', [new CategoryController($databaseFactory), 'getList']);
    $app->get('/api/courses/$id/categories/', [new CategoryController($databaseFactory), 'getCourseCategories']);
    $app->get('/api/categories/$id', [new CategoryController($databaseFactory), 'getUnique']);
    $app->post('/api/categories/', [new CategoryController($databaseFactory), 'postCategory']);
}

// Images
{
    $app->get('/api/images/$id', [new ImagesController($databaseFactory), 'getUnique']);
}

// Session
{
    $app->put('/api/session/', [new SessionController($databaseFactory), 'login']);
    $app->get('/api/session/', [new SessionController($databaseFactory), 'getCurrent']);
    $app->delete('/api/session/', [new SessionController($databaseFactory), 'logout']);
}

$app->run();