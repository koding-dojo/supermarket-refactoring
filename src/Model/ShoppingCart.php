<?php

namespace Supermarket\Model;

use Ds\Map;

class ShoppingCart
{
    /** @var ProductQuantity[] */
    private array $items = [];

    /** @var Map [Product => quantity] */
    private Map $productQuantities;

    public function __construct()
    {
        $this->productQuantities = new Map();
    }

    public function addItem(Product $product): void
    {
        $this->addItemQuantity($product, 1.0);
    }

    /**
     * @return ProductQuantity[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItemQuantity(Product $product, float $quantity): void
    {
        $this->items[] = new ProductQuantity($product, $quantity);
        if ($this->productQuantities->hasKey($product)) {
            $newAmount = $this->productQuantities[$product] + $quantity;
            $this->productQuantities[$product] = $newAmount;
        } else {
            $this->productQuantities[$product] = $quantity;
        }
    }

    public function getProductQuantities(): Map
    {
        return $this->productQuantities;
    }
}
