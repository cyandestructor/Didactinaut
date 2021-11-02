<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Resource;
use Didactinaut\Models\ResourceDao;

class ResourcesController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postResource(Request $request, Response $response, $args)
    {
        $result = [];

        $contentType = $request->getHeaderLine('Content-Type');
        $name = $request->getHeaderLine('X-Resource-Name');

        $lessonID = $request->getAttribute('id');

        $content = $request->getBody();
        if(!$content || $content->getSize() === 0){
            $result['message'] = 'The resource file was not specified';
            $response->getBody()->write(json_encode($result));
            return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
        }

        $resourceDAO = new ResourceDAO($this->dbFactory->create());

        $resource = new Resource();
        $resource->content = $content;
        $resource->name = $name;
        $resource->contentType = $contentType;
        $resource->lessonId = $lessonID;

        $result['id'] = $resourceDAO->addResource($resource);

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function deleteResource(Request $request, Response $response, $args)
    {
        $resourceID = $request->getAttribute('id');

        $resourceDAO = new ResourceDAO($this->dbFactory->create());
        $resourceDAO->deleteResource($resourceID);

        return $response;
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $resourceID = $request->getAttribute('id');

        $resourceDAO = new ResourceDAO($this->dbFactory->create());
        $resource = $resourceDAO->getResource($resourceID);

        if(!$resource || !$resource->content){
            return $response
                        ->withStatus(404);
        }

        $response->getBody()->write($resource->content);
        return $response
                    ->withHeader('Content-Type', $resource->contentType);
    }

    public function getLessonResources(Request $request, Response $response, $args)
    {
        $lessonID = $request->getAttribute('id');

        $resourceDAO = new ResourceDAO($this->dbFactory->create());

        $resources = $resourceDAO->getLessonResources($lessonID);

        $result = [];
        foreach ($resources as $resource) {
            $element = [];

            $element['id'] = $resource->id;
            $element['contentType'] = $resource->contentType;
            $element['name'] = $resource->name;
            $element['link'] = "/api/resources/$resource->id";

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}