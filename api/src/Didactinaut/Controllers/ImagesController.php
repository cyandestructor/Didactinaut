<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Didactinaut\Models\ImageDao;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

class ImagesController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $imageId = $request->getAttribute('id');

        $imageDAO = new ImageDao($this->dbFactory->create());
        $image = $imageDAO->get($imageId);
        if(!$image || !$image->content){
            return $response->withStatus(404);
        }

        $response->getBody()->write($image->content);
        return $response
                    ->withHeader('Content-Type', $image->contentType);
    }
}
