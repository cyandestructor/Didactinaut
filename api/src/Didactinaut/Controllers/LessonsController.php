<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Lesson;
use Didactinaut\Models\LessonDao;

class LessonsController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postLesson(Request $request, Response $response, $args)
    {
        $sectionID = $request->getAttribute('id');

        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // Lesson creation
        $lessonDAO = new LessonDAO($this->dbFactory->create());

        $lesson = new Lesson();
        $lesson->title = $data['title'];
        $lesson->text = $data['text'];
        $lesson->sectionId = $sectionID;

        $result['id'] = $lessonDAO->addLesson($lesson);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function putLesson(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];

        $lessonID = $request->getAttribute('id');

        $lessonDAO = new LessonDAO($this->dbFactory->create());

        // Get the original data
        $original = $lessonDAO->getLesson($lessonID);
        if(!$original){
            $result['message'] = 'Lesson does not exist';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
        }

        // Get new data and validate
        $data = $request->getParsedBody();
        
        $data['title'] = $data['title'] ?? $original->title;

        // Lesson edition
        $lessonData = new Lesson();
        $lessonData->id = $lessonID;
        $lessonData->title = $data['title'];
        $lessonData->text = $data['text'] ?? $original->text;

        $lessonDAO->editLesson($lessonData);

        // Prepare the return data
        $result['old'] = [
            'id' => $original->id,
            'title' => $original->title,
            'text' => $original->text,
        ];

        $result['new'] = [
            'id' => $lessonData->id,
            'title' => $lessonData->title,
            'text' => $lessonData->text,
        ];
        
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $lessonID = $request->getAttribute('id');

        $lessonDAO = new LessonDAO($this->dbFactory->create());
        $lesson = $lessonDAO->getLesson($lessonID);

        if(!$lesson){
            return $response
                        ->withStatus(404);
        }

        $result = [];
        $result['id'] = $lesson->id;
        $result['title'] = $lesson->title;
        $result['text'] = $lesson->text;
        $result['duration'] = $lesson->duration;
        $result['sectionId'] = $lesson->sectionId;
        $result['courseId'] = $lesson->courseId;
        $result['video'] = $lesson->extra['videoAddress'];

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getSectionLessons(Request $request, Response $response, $args)
    {
        $sectionID = $request->getAttribute('id');
        $queryParams = $request->getQueryParams();

        $lessonDAO = new LessonDAO($this->dbFactory->create());

        $lessons = [];

        if(isset($queryParams['userId'])){
            $lessons = $lessonDAO->getSectionUserLessons($sectionID, $queryParams['userId']);
        }
        else{
            $lessons = $lessonDAO->getSectionLessons($sectionID);
        }

        $result = [];
        foreach ($lessons as $lesson) {
            $element = [];

            $element['id'] = $lesson->id;
            $element['title'] = $lesson->title;
            $element['duration'] = $lesson->duration;
            $element['sectionId'] = $lesson->sectionId;
            $element['courseId'] = $lesson->courseId;

            if(isset($queryParams['userId'])){
                $element['completed'] = (bool) $lesson->extra['completed'];
            }

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function setCompleted(Request $request, Response $response, $args)
    {
        $userID = $request->getAttribute('userId');
        $lessonID = $request->getAttribute('lessonId');

        $queryParams = $request->getQueryParams();

        if(!isset($queryParams['completed'])){
            $response->getBody()->write(json_encode(['message' => 'Parameter "completed" must be specified']));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $lessonDAO = new LessonDAO($this->dbFactory->create());

        $completed = strtolower($queryParams['completed']) == 'true' ? true : false;

        $lessonDAO->setLessonCompleted($userID, $lessonID, $completed);

        return $response;
    }
}