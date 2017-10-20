<?php

namespace Cart\Events;

use Cart\Models\Order;
use Cart\Basket\Basket;

class OrderWasCreated extends Event
{
    public $order;

    public $basket;

    public function __construct(Order $order, Basket $basket)
    {
        $this->order = $order;
        $this->basket = $basket;
    }
}
