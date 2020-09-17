<?php

namespace Supermarket\Model;

class ThreeForTwoOffer implements Offer
{
    /**
     * @inheritDoc
     */
    public function getDiscount(float $quantity, float $unitPrice): float
    {
        $quantityAsInt = (int) $quantity;
        if ($quantityAsInt <= 2) {
            return 0.0;
        }

        $numberOfXs = intdiv($quantity, 3);
        $discountAmount = $quantity * $unitPrice - ($numberOfXs * 2 * $unitPrice + $quantityAsInt % 3 * $unitPrice);
        return -$discountAmount;
    }

    public function getDescription(): string
    {
        return "3 for 2";
    }
}
