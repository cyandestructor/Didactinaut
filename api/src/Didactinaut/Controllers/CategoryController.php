<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Category;
use Didactinaut\Models\CategoryDao;

class CategoryController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postCategory(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // Category creation
        $categoryDAO = new CategoryDAO($this->dbFactory->create());

        $category = new Category();
        $category->name = $data['name'];
        $category->description = $data['description'];

        $result['id'] = $categoryDAO->create($category);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $categoryID = $request->getAttribute('id');

        $categoryDAO = new CategoryDAO($this->dbFactory->create());
        $category = $categoryDAO->getCategory($categoryID);

        if(!$category){
            return $response
                        ->withStatus(404);
        }

        $result = [];
        $result['id'] = $category->id;
        $result['name'] = $category->name;
        $result['description'] = $category->description;

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getList(Request $request, Response $response, $args)
    {
        $categoryDAO = new CategoryDAO($this->dbFactory->create());

        $categories = $categoryDAO->getCategories();

        $result = [];
        foreach ($categories as $category) {
            $element = [];

            $element['id'] = $category->id;
            $element['name'] = $category->name;
            $element['description'] = $category->description;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }

    public function getCourseCategories(Request $request, Response $response, $args)
    {
        $courseID = $request->getAttribute('id');

        $categoryDAO = new CategoryDAO($this->dbFactory->create());

        $categories = $categoryDAO->getCourseCategories($courseID);

        $result = [];
        foreach ($categories as $category) {
            $element = [];

            $element['id'] = $category->id;
            $element['name'] = $category->name;
            $element['description'] = $category->description;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}