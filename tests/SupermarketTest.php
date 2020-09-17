<?php

namespace Tests;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Supermarket\Model\{PercentageOffer,
    Teller,
    Product,
    ProductUnit,
    ShoppingCart,
    ThreeForTwoOffer,
    XForAmount};
use Supermarket\ReceiptPrinter;

class SupermarketTest extends TestCase
{
    private Product $toothbrush;
    private Product $rice;
    private Product $apples;
    private Product $cherryTomatoes;
    private ShoppingCart $cart;
    private Teller $teller;
    private ReceiptPrinter $printer;

    public function setUp(): void
    {
        parent::setUp();

        $catalog = new FakeCatalog();
        $this->cart = new ShoppingCart();
        $this->teller = new Teller($catalog);

        $this->toothbrush = new Product('toothbrush', ProductUnit::EACH());
        $catalog->addProduct($this->toothbrush, 0.99);
        $this->rice = new Product('rice', ProductUnit::EACH());
        $catalog->addProduct($this->rice, 2.99);
        $this->apples = new Product('apples', ProductUnit::KILO());
        $catalog->addProduct($this->apples, 1.99);
        $this->cherryTomatoes = new Product('cherry tomato box', ProductUnit::EACH());
        $catalog->addProduct($this->cherryTomatoes, 0.69);

        $this->printer = new ReceiptPrinter(40);
    }

    /** @test */
    public function anEmptyShoppingCartShouldCostNothing()
    {
        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function oneNormalItem()
    {
        $this->cart->addItem($this->toothbrush);

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function twoNormalItems()
    {
        $this->cart->addItem($this->toothbrush);
        $this->cart->addItem($this->rice);

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function buyTwoGetOneFree()
    {
        $this->cart->addItem($this->toothbrush);
        $this->cart->addItem($this->toothbrush);
        $this->cart->addItem($this->toothbrush);
        $this->teller->addOffer($this->toothbrush, new ThreeForTwoOffer());

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function buyTwoGetOneFreeButInsufficientInBasket()
    {
        $this->cart->addItem($this->toothbrush);
        $this->teller->addOffer($this->toothbrush, new ThreeForTwoOffer());

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function buyFiveGetOneFree()
    {
        $this->cart->addItem($this->toothbrush);
        $this->cart->addItem($this->toothbrush);
        $this->cart->addItem($this->toothbrush);
        $this->cart->addItem($this->toothbrush);
        $this->cart->addItem($this->toothbrush);
        $this->teller->addOffer($this->toothbrush, new ThreeForTwoOffer());

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function looseWeightProduct()
    {
        $this->cart->addItemQuantity($this->apples, 0.5);

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function percentDiscount()
    {
        $this->cart->addItem($this->rice);
        $this->teller->addOffer($this->rice, new PercentageOffer(10.0));

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function xForYDiscount()
    {
        $this->cart->addItem($this->cherryTomatoes);
        $this->cart->addItem($this->cherryTomatoes);
        $this->teller->addOffer($this->cherryTomatoes, new XForAmount(2, 0.99));

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function xForYDiscountWithInsufficientInBasket()
    {
        $this->cart->addItem($this->cherryTomatoes);
        $this->teller->addOffer($this->cherryTomatoes, new XForAmount(2, 0.99));

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function fiveForYDiscount()
    {
        $this->cart->addItemQuantity($this->apples, 5);
        $this->teller->addOffer($this->apples, new XForAmount(5, 6.99));

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function fiveForYDiscountWithSix()
    {
        $this->cart->addItemQuantity($this->apples, 6);
        $this->teller->addOffer($this->apples, new XForAmount(5, 6.99));

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function fiveForYDiscountWithSixteen()
    {
        $this->cart->addItemQuantity($this->apples, 16);
        $this->teller->addOffer($this->apples, new XForAmount(5, 6.99));

        Approvals::verifyString($this->printReceipt());
    }

    /** @test */
    public function fiveForYDiscountWithFour()
    {
        $this->cart->addItemQuantity($this->apples, 4);
        $this->teller->addOffer($this->apples, new XForAmount(5, 6.99));

        Approvals::verifyString($this->printReceipt());
    }

    /**
     * @return string
     */
    private function printReceipt(): string
    {
        $receipt = $this->teller->checkoutArticlesFrom($this->cart);
        return $this->printer->printReceipt($receipt);
    }
}
