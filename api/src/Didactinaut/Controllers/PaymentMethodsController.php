<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\PaymentMethodDao;

class PaymentMethodsController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function getList(Request $request, Response $response, $args)
    {
        $paymentMethodDao = new PaymentMethodDao($this->dbFactory->create());

        $methods = $paymentMethodDao->getPaymentMethods();

        $result = [];
        foreach ($methods as $method) {
            $element = [];

            $element['id'] = $method->id;
            $element['name'] = $method->name;

            $result[] = $element;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json');
    }
}