<?php
namespace Didactinaut\Models;

use Didactinaut\Configuration\Database\DatabaseInterface;
use Didactinaut\Models\Dto\PaymentMethod;

class PaymentMethodDao
{
    private $connection;

    public function __construct(DatabaseInterface $db)
    {
        $this->connection = $db->connect();
    }

    public function getPaymentMethods()
    {
        $methods = [];

        $sql = 'CALL GetPaymentMethods()';

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        while($row = $statement->fetch()){
            $method = new PaymentMethod();

            $method->id = $row['payment_method_id'];
            $method->name = $row['payment_method_name'];

            $methods[] = $method;
        }

        return $methods;
    }
}