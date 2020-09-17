<?php

namespace Supermarket\Model;

use Ds\Map;

class Teller
{
    private SupermarketCatalog $catalog;

    /** @var Map<Product, Offer> */
    private Map $offers;

    public function __construct(SupermarketCatalog $catalog)
    {
        $this->catalog = $catalog;
        $this->offers = new Map();
    }

    public function addOffer(Product $product, Offer $offer)
    {
        $this->offers[$product] = $offer;
    }

    public function checkoutArticlesFrom(ShoppingCart $cart): Receipt
    {
        $receipt = new Receipt();
        $productQuantities = $cart->getItems();
        foreach ($productQuantities as $pq) {
            $product = $pq->getProduct();
            $quantity = $pq->getQuantity();
            $unitPrice = $this->catalog->getUnitPrice($product);
            $price = $quantity * $unitPrice;
            $receipt->addProduct($product, $quantity, $unitPrice, $price);
        }

        /**
         * @var Product $product
         * @var float $productQuantity
         */
        foreach ($cart->getProductQuantities() as $product => $quantity) {
            if ($this->offers->hasKey($product)) {
                /** @var Offer $offer */
                $offer = $this->offers[$product];
                $unitPrice = $this->catalog->getUnitPrice($product);
                $discount = $offer->getDiscount($quantity, $unitPrice);
                if ($discount) {
                    $receipt->addDiscount(new Discount($product, $offer->getDescription(), $discount));
                }
            }
        }

        return $receipt;
    }
}
