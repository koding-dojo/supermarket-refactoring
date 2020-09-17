<?php

namespace Supermarket\Model;

interface Offer
{
    /**
     * @param float   $quantity
     * @param float   $unitPrice
     * @return float
     */
    public function getDiscount(float $quantity, float $unitPrice): float;

    public function getDescription(): string;
}
