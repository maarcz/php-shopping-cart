<?php

namespace Cart\Basket;

use Cart\Models\Product;
use Cart\Support\Storage\Contracts\StorageInterface;
use Cart\Basket\Exceptions\QuantityExceededException;

class Basket
{
    protected $storage;

    protected $product;

    public function __construct(StorageInterface $storage, Product $product)
    {
        $this->storage = $storage;
        $this->product = $product;
    }

    public function add(Product $product, $quantity)
    {
        if ($this->has($product)) {
            $quantity = $this->get($product)['quantity'] + $quantity;
        }

        $this->update($product, $quantity);
    }

    public function update(Product $product, $quantity)
    {
        if (!$this->product->find($product->id)->hasStock($quantity)) {
            throw new QuantityExceededException;
        }

        if ($quantity === 0) {
            $this->remove($product);
            return;
        }

        $this->storage->set($product->id, [
            'product_id' => (int) $product->id,
            'quantity' => (int) $quantity,
        ]);
    }

    public function remove(Product $product)
    {
        $this->storage->unset($product->id);
    }

    public function has(Product $product)
    {
        return $this->storage->exists($product->id);
    }

    public function get(Product $product)
    {
        return $this->storage->get($product->id);
    }

    public function clear()
    {
        $this->storage->clear();
    }

    public function all()
    {
        $ids = [];
        $items = [];

        foreach ($this->storage->all() as $product) {
            $ids[] = $product['product_id'];
        }

        $products = $this->product->find($ids);

        foreach ($products as $product) {
            $product->quantity = $this->get($product)['quantity'];
            $items[] = $product;
        }

        return $items;
    }

    public function itemCount()
    {
        return count($this->storage);
    }
}
