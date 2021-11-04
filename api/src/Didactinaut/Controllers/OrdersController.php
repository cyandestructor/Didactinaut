<?php
namespace Didactinaut\Controllers;

use Didactinaut\Factories\Database\DatabaseFactoryInterface;
use Small\Interfaces\RequestInterface as Request;
use Small\Interfaces\ResponseInterface as Response;

use Didactinaut\Models\Dto\Order;
use Didactinaut\Models\OrderDao;

class OrdersController
{
    private $dbFactory;

    public function __construct(DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    public function postOrder(Request $request, Response $response, $args)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if(!$contentType || $contentType != 'application/json'){
            return $response
                        ->withStatus(415);
        }

        $result = [];
        $data = $request->getParsedBody();

        // Order placement
        $orderDao = new OrderDao($this->dbFactory->create());

        $order = new Order();

        $order->ordererId = $data['ordererId'];
        $order->paymentMethodId = $data['paymentMethod'];
        $order->products = $data['products'];

        $result['id'] = $orderDao->place($order);
        $response->getBody()->write(json_encode($result));
        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
    }
}