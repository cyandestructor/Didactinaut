<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Course;
use Didactinaut\Models\CourseDao;

class CoursesController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $courseID = $request->getAttribute('id');

        $courseDAO = new CourseDAO($this->dbFactory->create());
        $course = $courseDAO->getCourse($courseID);

        if(!$course){
            return $response
                        ->withStatus(404);
        }

        $result = [];
        $result['id'] = $course->id;
        $courseImage = $course->imageId; 
        $result['image'] = "/api/images/$courseImage";
        $result['title'] = $course->title;
        $result['description'] = $course->description;
        $result['productId'] = $course->product['id'];
        $result['price'] = $course->product['price'];
        $result['instructor'] = [
            'id' => $course->instructor['id'],
            'name' => $course->instructor['name'],
            'lastname' => $course->instructor['lastname']
        ];
        $result['publicationDate'] = $course->publicationDate;
        $result['lastUpdate'] = $course->lastUpdate;
        $result['totalStudents'] = $course->extra['totalStudents'];
        $result['totalLessons'] = $course->extra['totalLessons'];
        $result['score'] = $course->extra['score'];
        $result['published'] = (bool)$course->published;

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getList(Request $request, Response $response, $args)
    {
        $queryParams = $request->getQueryParams();

        if(!isset($queryParams['count'])){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "count" must be defined'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        if(!is_numeric($queryParams['count']) || $queryParams['count'] < 0){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "count" must be a positive number'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $limit = $queryParams['count'];
        $page = $queryParams['page'] ?? 1;

        if(!is_numeric($page) || $page < 0){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "page" must be a positive number'
            ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $offset = ($page - 1) * $limit;

        $orderBy = $queryParams['orderBy'] ?? '';

        if(!in_array($orderBy, ['sales', 'publication', 'score', ''])){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "orderBy" is invalid'
            ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $courseDAO = new CourseDAO($this->dbFactory->create());

        $courses = $courseDAO->getCourses($limit, $offset, $orderBy);

        $result = [];
        foreach ($courses as $course) {
            $element = [];
            
            $element['id'] = $course->id;
            $courseImage = $course->imageId; 
            $element['image'] = "/api/images/$courseImage";
            $element['title'] = $course->title;
            $element['description'] = $course->description;
            $element['productId'] = $course->product['id'];
            $element['price'] = $course->product['price'];
            $element['instructor'] = [
                'id' => $course->instructor['id'],
                'name' => $course->instructor['name'],
                'lastname' => $course->instructor['lastname']
            ];
            $element['score'] = $course->extra['score'];
            $element['published'] = (bool)$course->published;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function postCourse(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // Course creation
        $courseDAO = new CourseDAO($this->dbFactory->create());

        $courseData = new Course();
        $courseData->title = $data['title'];
        $courseData->description = $data['description'];
        $courseData->product['price'] = $data['price'];
        $courseData->instructor['id'] = $data['instructorId'];

        $result['id'] = $courseDAO->createCourse($courseData);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function putCourse(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        $courseID = $request->getAttribute('id');
        $courseDAO = new CourseDAO($this->dbFactory->create());
        
        // Get the original data

        $original = $courseDAO->getCourse($courseID);
        if(!$original){
            $result['message'] = 'Course does not exist';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
        }

        // Course edition

        $courseData = new Course();
        $courseData->id = $courseID;
        $courseData->title = $data['title'] ?? $original->title;
        $courseData->description = $data['description'] ?? $original->description;
        $courseData->product['price'] = $data['price'] ?? $original->product['price'];
        $courseData->published = $data['published'] ?? $original->published;

        $courseDAO->editCourse($courseData);
        
        // Prepare the return data
        $result['old'] = [
            'id' => $original->id,
            'title' => $original->title,
            'description' => $original->description,
            'price' => $original->product['price'],
            'published' => (bool)$original->published
        ];

        $result['new'] = [
            'id' => $courseData->id,
            'title' => $courseData->title,
            'description' => $courseData->description,
            'price' => $courseData->product['price'],
            'published' => (bool)$courseData->published
        ];
        
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function putCourseImage(Request $request, Response $response, $args)
    {
        $result = [];
        
        $supportedMediaTypes = ['image/jpeg', 'image/png'];
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || !in_array($contentType, $supportedMediaTypes)){
            return $response
                        ->withStatus(415);
        }

        $courseID = $request->getAttribute('id');

        // TODO: Check if the course exists
        $image = $request->getBody();
        if(!$image || $image->getSize() === 0){
            $result['message'] = 'The image file was not specified';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $courseDAO = new CourseDAO($this->dbFactory->create());

        $courseDAO->setCourseImage($courseID, $image, $contentType);

        return $response;
    }

    public function addCourseCategory(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $data = $request->getParsedBody();

        $courseID = $request->getAttribute('id');

        $courseDAO = new CourseDAO($this->dbFactory->create());
        $courseDAO->addCategory($courseID, $data['categoryId']);

        return $response
                    ->withStatus(201);
    }

    public function deleteCourseCategory(Request $request, Response $response, $args)
    {
        $courseID = $request->getAttribute('id');
        $categoryID = $request->getAttribute('categoryId');
        
        $courseDAO = new CourseDAO($this->dbFactory->create());
        $courseDAO->deleteCategory($courseID, $categoryID);

        return $response;
    }

    public function getInstructorCourses(Request $request, Response $response, $args)
    {
        $userID = $request->getAttribute('id');
        $queryParams = $request->getQueryParams();

        if(!isset($queryParams['count'])){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "count" must be defined'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        if(!is_numeric($queryParams['count']) || $queryParams['count'] < 0){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "count" must be a positive number'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $limit = $queryParams['count'];
        $page = $queryParams['page'] ?? 1;

        if(!is_numeric($page) || $page < 0){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "page" must be a positive number'
            ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $offset = ($page - 1) * $limit;

        $onlyPublished = isset($queryParams['public']) && strtolower($queryParams['public']) == 'true';

        $courseDAO = new CourseDAO($this->dbFactory->create());

        $courses = $courseDAO->getInstructorCourses($userID, $limit, $offset, $onlyPublished);

        $result = [];
        foreach ($courses as $course) {
            $element = [];
            $element['id'] = $course->id;
            $courseImage = $course->imageId; 
            $element['image'] = "/api/images/$courseImage";
            $element['title'] = $course->title;
            $element['description'] = $course->description;
            $element['productId'] = $course->product['id'];
            $element['price'] = $course->product['price'];
            $element['instructor'] = [
                'id' => $course->instructor['id'],
                'name' => $course->instructor['name'],
                'lastname' => $course->instructor['lastname']
            ];
            $element['score'] = $course->extra['score'];
            $element['published'] = (bool)$course->published;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getUserCourses(Request $request, Response $response, $args)
    {
        $userID = $request->getAttribute('id');
        $queryParams = $request->getQueryParams();

        if(!isset($queryParams['count'])){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "count" must be defined'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        if(!is_numeric($queryParams['count']) || $queryParams['count'] < 0){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "count" must be a positive number'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $limit = $queryParams['count'];
        $page = $queryParams['page'] ?? 1;

        if(!is_numeric($page) || $page < 0){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "page" must be a positive number'
            ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $offset = ($page - 1) * $limit;

        $courseDAO = new CourseDAO($this->dbFactory->create());

        $courses = $courseDAO->getUserCourses($userID, $limit, $offset);

        $result = [];
        foreach ($courses as $course) {
            $element = [];

            $element['id'] = $course->id;
            $courseImage = $course->imageId;
            $element['image'] = "/api/images/$courseImage";
            $element['title'] = $course->title;
            $element['description'] = $course->description;
            $element['productId'] = $course->product['id'];
            $element['price'] = $course->product['price'];
            $instructor = [];
            $instructor['id'] = $course->instructor['id'];
            $instructor['name'] = $course->instructor['name'];
            $instructor['lastname'] = $course->instructor['lastname'];
            $element['instructor'] = $instructor;
            $element['score'] = $course->extra['score'];
            $element['totalLessons'] = $course->extra['totalLessons'];
            $element['enrollDate'] = $course->extra['enrollDate'];
            $element['lastTimeChecked'] = $course->extra['lastTimeChecked'];
            $element['certificateId'] = $course->extra['certificateId'];
            $element['totalCompletedLessons'] = $course->extra['totalCompletedLessons'];
            $element['completionRatio'] = $course->extra['totalCompletedLessons'] / max($course->extra['totalLessons'], 1);
            $element['published'] = (bool)$course->published;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function putLastTimeChecked(Request $request, Response $response, $args)
    {
        $userID = $request->getAttribute('userId');
        $courseID = $request->getAttribute('courseId');

        $courseDao = new CourseDao($this->dbFactory->create());

        $courseDao->setUserLastTimeChecked($userID, $courseID);

        return $response;
    }

}