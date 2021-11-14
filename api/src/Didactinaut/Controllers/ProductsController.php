<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Product;
use Didactinaut\Models\ProductDao;

class ProductsController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function getUnique(Request $request, Response $response, $args)
    {
        $productId = $request->getAttribute('id');

        $productDao = new ProductDao($this->dbFactory->create());
        $product = $productDao->get($productId);

        if(!$product){
            return $response
                        ->withStatus(404);
        }

        $result = [];
        $result['id'] = $product->id;
        $result['name'] = $product->name;
        $result['price'] = $product->price;
        $image = $product->image;
        $result['image'] = "/api/images/$image";

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}