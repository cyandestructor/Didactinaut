<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Section;
use Didactinaut\Models\SectionDao;

class SectionsController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postSection(Request $request, Response $response, $args)
    {
        $courseID = $request->getAttribute('id');
        
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // Section creation
        $sectionDAO = new SectionDAO($this->dbFactory->create());

        $section = new Section();
        $section->title = $data['title'];
        $section->courseId = $courseID;
        $section->product['price'] = $data['price'];

        $result['id'] = $sectionDAO->addSection($section);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function putSection(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];

        $sectionID = $request->getAttribute('id');

        $sectionDAO = new SectionDAO($this->dbFactory->create());

        // Get the original data
        $original = $sectionDAO->getSection($sectionID);
        if(!$original){
            $result['message'] = 'Section does not exist';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
        }

        // Get new data and validate
        $data = $request->getParsedBody();
        
        $data['title'] = $data['title'] ?? $original->title;
        $data['price'] = $data['price'] ?? $original->product['price'];

        // Section edition
        $sectionData = new Section();
        $sectionData->id = $sectionID;
        $sectionData->title = $data['title'];
        $sectionData->product['price'] = $data['price'];

        $sectionDAO->editSection($sectionData);

        // Prepare the return data
        $result['old'] = [
            'id' => $original->id,
            'title' => $original->title,
            'price' => $original->price
        ];

        $result['new'] = [
            'id' => $sectionData->id,
            'title' => $sectionData->title,
            'price' => $sectionData->price
        ];
        
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getCourseSections(Request $request, Response $response, $args)
    {
        $courseID = $request->getAttribute('id');
        $queryParams = $request->getQueryParams();

        $sectionDAO = new SectionDAO($this->dbFactory->create());

        $sections = [];

        if(isset($queryParams['userId'])){
            $sections = $sectionDAO->getUserCourseSections($courseID, $queryParams['userId']);
        }
        else{
            $sections = $sectionDAO->getCourseSections($courseID);
        }

        $result = [];
        foreach ($sections as $section) {
            $element = [];

            $element['id'] = $section->id;
            $element['title'] = $section->title;
            $element['productId'] = $section->product['id'];
            $element['price'] = $section->product['price'];
            $element['courseId'] = $section->courseId;

            if(isset($queryParams['userId'])){
                $element['accessible'] = (bool) $section->extra['accesible'];
            }

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $sectionID = $request->getAttribute('id');

        $sectionDAO = new SectionDAO($this->dbFactory->create());
        $section = $sectionDAO->getSection($sectionID);

        if(!$section){
            return $response
                        ->withStatus(404);
        }

        $result = [];
        $result['id'] = $section->id;
        $result['title'] = $section->title;
        $result['productId'] = $section->product['id'];
        $result['price'] = $section->product['price'];
        $result['courseId'] = $section->courseId;

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}