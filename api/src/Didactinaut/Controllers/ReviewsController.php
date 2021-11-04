<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Review;
use Didactinaut\Models\ReviewDao;

class ReviewsController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postReview(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        $courseID = $request->getAttribute('id');

        // Review creation
        $reviewDAO = new ReviewDAO($this->dbFactory->create());

        $review = new Review();
        $review->body = $data['body'];
        $review->courseId = $courseID;
        $review->user['id'] = $data['userId'];
        $review->score = $data['score'];

        $result['id'] = $reviewDAO->createReview($review);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function putReview(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];

        $reviewID = $request->getAttribute('id');

        $reviewDAO = new ReviewDAO($this->dbFactory->create());

        // Get the original data
        $original = $reviewDAO->getReview($reviewID);
        if(!$original){
            $result['message'] = 'Review does not exist';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
        }

        // Get new data
        $data = $request->getParsedBody();

        // Review edition
        $reviewData = new Review();
        $reviewData->id = $reviewID;
        $reviewData->body = $data['body'] ?? $original->body;
        $reviewData->score = $data['score'] ?? $original->score;
        $reviewData->published = $data['published'] ?? $original->published;

        $reviewDAO->editReview($reviewData);

        // Prepare the return data
        $result['old'] = [
            'id' => $original->id,
            'body' => $original->body,
            'score' => $original->score,
            'published' => (bool)$original->published
        ];

        $result['new'] = [
            'id' => $reviewData->id,
            'body' => $reviewData->body,
            'score' => $reviewData->score,
            'published' => (bool)$reviewData->published
        ];
        
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getCourseReviews(Request $request, Response $response, $args)
    {
        $courseID = $request->getAttribute('id');

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

        $reviewDAO = new ReviewDAO($this->dbFactory->create());

        $reviews = $reviewDAO->getCourseReviews($courseID, $limit, $offset);

        $result = [];
        foreach ($reviews as $review) {
            $element = [];

            $element['id'] = $review->id;
            $element['body'] = $review->body;
            $element['date'] = $review->date;
            $element['user'] = $review->user;
            $element['score'] = $review->score;
            $element['courseId'] = $review->courseId;
            $element['published'] = $review->published;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $reviewID = $request->getAttribute('id');

        $reviewDAO = new ReviewDAO($this->dbFactory->create());
        $review = $reviewDAO->getReview($reviewID);

        if(!$review){
            return $response
                        ->withStatus(404);
        }

        $result = [];
        $result['id'] = $review->id;
        $result['body'] = $review->body;
        $result['date'] = $review->date;
        $result['user'] = $review->user;
        $result['score'] = $review->score;
        $result['courseId'] = $review->courseId;
        $result['published'] = (bool)$review->published;

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}