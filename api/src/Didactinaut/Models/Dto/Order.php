<?php
namespace Didactinaut\Models\Dto;

class Order
{
    public $ordererId;
    public $paymentMethodId;
    public $products = [];
}