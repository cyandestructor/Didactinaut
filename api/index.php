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
use Didactinaut\Controllers\ResourcesController;
use Didactinaut\Controllers\VideosController;
use Didactinaut\Controllers\ChatsController;
use Didactinaut\Controllers\MessagesController;
use Didactinaut\Controllers\OrdersController;
use Didactinaut\Controllers\ReportsController;
use Didactinaut\Controllers\ReviewsController;
use Didactinaut\Controllers\PaymentMethodsController;
use Didactinaut\Controllers\CertificatesController;

use Didactinaut\Configuration\VideoUpload\AzureBlobStorageVideoUploader;
use Didactinaut\Factories\Database\MySQLDatabaseFactory;

$databaseFactory = new MySQLDatabaseFactory('localhost', 'didactinaut_dev');
$videoUploader = new AzureBlobStorageVideoUploader();

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

// Chats
{
    $app->get('/api/users/$id/chats/', [new ChatsController($databaseFactory), 'getUserChats']);
    $app->post('/api/users/$id/chats/', [new ChatsController($databaseFactory), 'postChat']);
}

// Messages
{
    $app->get('/api/chats/$id/messages/', [new MessagesController($databaseFactory), 'getChatMessages']);
    $app->post('/api/chats/$id/messages/', [new MessagesController($databaseFactory), 'postMessage']);
}

// Resources
{
    $app->get('/api/resources/$id', [new ResourcesController($databaseFactory), 'getUnique']);
    $app->get('/api/lessons/$id/resources/', [new ResourcesController($databaseFactory), 'getLessonResources']);
    $app->post('/api/lessons/$id/resources/', [new ResourcesController($databaseFactory), 'postResource']);
    $app->delete('/api/resources/$id', [new ResourcesController($databaseFactory), 'deleteResource']);
}

// Videos
{
    $app->get('/api/lessons/$id/video/', [new VideosController($databaseFactory, $videoUploader), 'getLessonVideo']);
    $app->put('/api/lessons/$id/video/', [new VideosController($databaseFactory, $videoUploader), 'putLessonVideo']);
    $app->delete('/api/videos/$id', [new VideosController($databaseFactory, $videoUploader), 'deleteVideo']);
}

// Categories
{
    $app->get('/api/categories/', [new CategoryController($databaseFactory), 'getList']);
    $app->get('/api/courses/$id/categories/', [new CategoryController($databaseFactory), 'getCourseCategories']);
    $app->get('/api/categories/$id', [new CategoryController($databaseFactory), 'getUnique']);
    $app->post('/api/categories/', [new CategoryController($databaseFactory), 'postCategory']);
}

// Reviews
{
    $app->get('/api/reviews/$id', [new ReviewsController($databaseFactory), 'getUnique']);
    $app->get('/api/courses/$id/reviews/', [new ReviewsController($databaseFactory), 'getCourseReviews']);
    $app->post('/api/courses/$id/reviews/', [new ReviewsController($databaseFactory), 'postReview']);
    $app->put('/api/reviews/$id', [new ReviewsController($databaseFactory), 'putReview']);
}

// Certificates
{
    $app->get('/api/certificates/$id', [new CertificatesController($databaseFactory), 'getUnique']);
    $app->get('/api/users/$id/certificates/', [new CertificatesController($databaseFactory), 'getUserCertificates']);
    $app->post('/api/certificates/', [new CertificatesController($databaseFactory), 'postCertificate']);
}

// Images
{
    $app->get('/api/images/$id', [new ImagesController($databaseFactory), 'getUnique']);
}

// Orders
{
    $app->post('/api/orders/', [new OrdersController($databaseFactory), 'postOrder']);
}

// Report
{
    $app->get('/api/reports/', [new ReportsController($databaseFactory), 'getReports']);
}

// Payment Methods
{
    $app->get('/api/payment-methods/', [new PaymentMethodsController($databaseFactory), 'getList']);
}

// Session
{
    $app->put('/api/session/', [new SessionController($databaseFactory), 'login']);
    $app->get('/api/session/', [new SessionController($databaseFactory), 'getCurrent']);
    $app->delete('/api/session/', [new SessionController($databaseFactory), 'logout']);
}

$app->run();