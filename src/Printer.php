<?php

namespace Supermarket;

use Supermarket\Model\Receipt;

interface Printer
{
    public function printReceipt(Receipt $receipt);
}