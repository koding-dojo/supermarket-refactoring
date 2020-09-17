<?php

namespace Supermarket\Model;

class XForAmount implements Offer
{
    private float $x;
    private float $amount;

    public function __construct(float $x, float $amount)
    {
        $this->x = $x;
        $this->amount = $amount;
    }

    public function getDiscount(float $quantity, float $unitPrice): float
    {
        $quantityAsInt = (int) $quantity;
        if ($quantityAsInt < $this->x) {
            return 0.0;
        }

        $total = $this->amount * intdiv($quantityAsInt, $this->x) + $quantityAsInt % $this->x * $unitPrice;
        return -1 * ($unitPrice * $quantity - $total);
    }

    public function getDescription(): string
    {
        return "$this->x for {$this->amount}";
    }
}
