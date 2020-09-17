<?php

namespace Supermarket\Model;

class PercentageOffer implements Offer
{
    private float $percent;

    public function __construct(float $percent)
    {
        $this->percent = $percent;
    }

    /**
     * @inheritDoc
     */
    public function getDiscount(float $quantity, float $unitPrice): float
    {
        return -$quantity * $unitPrice * $this->percent / 100.0;
    }

    public function getDescription(): string
    {
        return "{$this->percent}% off";
    }
}
