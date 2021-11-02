<?php
require_once __DIR__ . '/vendor/autoload.php';

use Small\Core\Application;

use Small\Middleware\BodyParserMiddleware;
use Small\Middleware\SessionMiddleware;

use Didactinaut\Controllers\UsersController;
use Didactinaut\Controllers\ImagesController;
use Didactinaut\Controllers\SessionController;
use Didactinaut\Controllers\CoursesController;
use Didactinaut\Controllers\CategoryController;
use Didactinaut\Controllers\SectionsController;
use Didactinaut\Controllers\LessonsController;
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

// Courses
{
    $app->get('/api/courses/$id', [new CoursesController($databaseFactory), 'getUnique']);
    $app->get('/api/courses/', [new CoursesController($databaseFactory), 'getList']);
    $app->get('/api/users/$id/courses/', [new CoursesController($databaseFactory), 'getInstructorCourses']);
    $app->post('/api/courses/', [new CoursesController($databaseFactory), 'postCourse']);
    $app->post('/api/courses/$id/categories/', [new CoursesController($databaseFactory), 'addCourseCategory']);
    $app->delete('/api/courses/$id/categories/$categoryId', [new CoursesController($databaseFactory), 'deleteCourseCategory']);
    $app->put('/api/courses/$id', [new CoursesController($databaseFactory), 'putCourse']);
    $app->put('/api/courses/$id/image', [new CoursesController($databaseFactory), 'putCourseImage']);
}

// Sections
{
    $app->get('/api/courses/$id/sections/', [new SectionsController($databaseFactory), 'getCourseSections']);
    $app->get('/api/sections/$id', [new SectionsController($databaseFactory), 'getUnique']);
    $app->put('/api/sections/$id', [new SectionsController($databaseFactory), 'putSection']);
    $app->post('/api/courses/$id/sections/', [new SectionsController($databaseFactory), 'postSection']);
}

// Lessons
{
    $app->get('/api/lessons/$id', [new LessonsController($databaseFactory), 'getUnique']);
    $app->get('/api/sections/$id/lessons/', [new LessonsController($databaseFactory), 'getSectionLessons']);
    $app->put('/api/lessons/$id', [new LessonsController($databaseFactory), 'putLesson']);
    $app->put('/api/users/$userId/lessons/$lessonId', [new LessonsController($databaseFactory), 'setCompleted']);
    $app->post('/api/sections/$id/lessons/', [new LessonsController($databaseFactory), 'postLesson']);
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