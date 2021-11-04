<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\Order;

class OrderDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function place(Order $order)
    {
        $id = $this->create($order);
        if ($id != -1) {
            foreach ($order->products as $product) {
                $this->addProduct($id, $product);
            }
        }

        return $id;
    }

    public function create(Order $order)
    {
        $orderId = -1;
        
        $sql = 'CALL PlaceOrder(?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $order->ordererId,
            $order->paymentMethodId
        ]);

        $statement->bindColumn(1, $orderId, \PDO::PARAM_INT);
        $statement->fetch(\PDO::FETCH_BOUND);

        return $orderId;
    }

    public function addProduct($orderId, $productId)
    {
        $sql = 'CALL AddProductToOrder(?, ?)';
        
        $statement = $this->connection->prepare($sql);
        
        $statement->execute([
            $orderId,
            $productId
        ]);
    }
}