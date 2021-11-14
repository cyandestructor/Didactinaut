<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Product;

class ProductDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function get($productId)
    {
        $product = new Product();

        $sql = 'CALL GetProductInfo(?)';

        $statement = $this->connection->prepare($sql);
        $statement->bindParam(1, $productId);
        $statement->execute();

        if($row = $statement->fetch()){
            $product->id = $row['product_id'];
            $product->price = $row['product_price'];
            $product->name = $row['product_name'];
            $product->image = $row['product_image'];
        }
        else{
            return null;
        }

        return $product;
    }
}