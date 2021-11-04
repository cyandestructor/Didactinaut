<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\ReportDao;
use Small\Core\Session;

class ReportsController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function getReports(Request $request, Response $response, $args)
    {
        $session = new Session();
        $user = $session->get('user');

        if(!$user){
            $response->getBody()->write(json_encode([
                'message' => 'Invalid user session'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(401);
        }

        $instructorId = $user['id'];

        $queryParams = $request->getQueryParams();

        if(!isset($queryParams['type'])){
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "type" must be defined. Possible values: sales, courseUsers'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $type = $queryParams['type'];

        if ($type == 'courseUsers' && !isset($queryParams['courseId'])) {
            $response->getBody()->write(json_encode([
                'message' => 'Parameter "courseId" must be defined when the type is "courseUsers"'
                ]));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $courseId = $queryParams['courseId'] ?? null;

        $result = [];

        $reportDao = new ReportDao($this->dbFactory->create());

        if ($type == 'sales') {
            $result = $reportDao->getInstructorSalesReport($instructorId);
        }
        else if ($type == 'courseUsers') {
            $result = $reportDao->getCourseUsersReport($courseId);
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}