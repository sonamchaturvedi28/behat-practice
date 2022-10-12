<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I am on the cart page
     */
    public function iAmOnTheCartPage()
    {
       $this->visitPath("/index.php?controller=order");
    }

    /**
     * @Given I add :product_name into the cart
     */
    public function iAddIntoTheCart($product_name)
    {
        $page = $this->getSession()->getPage();
        $product = $page->find('xpath','//a[@class="product_img_link" and @title="'.$product_name.'"]');
        $product->click();
    }

    /* Get product row based on Product Name
     */
    public function getProductRow($product_name)
    {
        $page = $this->getSession()->getPage();
        $product_row = $page->find('xpath','//a[text()="'.$product_name.'"]//ancestor::tr[1]');
        return $product_row;
    }

    /**
     * @Then I should see correct total price
     */
    public function iShouldSeeCorrectTotalPrice()
    {
       $page = $this->getSession()->getPage();
       $products_total = 0;
       $list_of_product_total = $page->findAll('css','td.cart_total');
       for($index=0;$index < count($list_of_product_total);$index++)
       {
           $products_total = $products_total + (float)str_replace("$", "",$list_of_product_total[$index]->getText());
       }

       $total_shipping = (float)str_replace("$", "",$page->find('css','#total_shipping')->getText());

       //Total tax field is available only on Shopping Cart summary page
       if($page->hasContent("Shopping-cart summary"))
       {
           $total_tax = (float)str_replace("$", "",$page->find('css','#total_tax')->getText());
       }
       $actual_cart_total = (float)str_replace("$", "",$page->find('css','#total_price_container')->getText());
       $expected_cart_total = $products_total + $total_shipping + $total_tax;
       Assert.assertEquals($actual_cart_total,$expected_cart_total,'Incorrect Total Cart Price result');
    }

    /**
     * @Then I should see Product details :
     */
    public function iShouldSeeProductDetails(TableNode $table)
    {
      $product_details = $table->getRowsHash();

      for ($index=0;$index<count($product_details['Product Name']);$index++)
      {
        $product_row = $this->getProductRow($product_details['Product Name']);
        $attr_colour_size = explode(", ", $product_row->find('css', '.cart_description > small > a')->getText());

        Assert.assertEquals($attr_colour_size[0],$product_details['Product Colour']);
        Assert.assertEquals($attr_colour_size[1],$product_details['Product Size']);
        Assert.assertEquals($product_row->find('css','.cart_unit > span > span')->getText(), $product_details['Product Price']);
        Assert.assertEquals($product_row->find('css','.cart_quantity > input')->getAttribute('value'), $product_details['Product Quantity']);
      }
    }

    /**
     * @Then I should see quantity of :product_name as :expected_qty
     */
    public function iShouldSeeQuantityOfAs($product_name, $expected_qty)
    {
        $product_row = $this->getProductRow($product_name);
        $actual_qty = $product_row->find('css','.cart_quantity > input')->getAttribute('value');
        Assert.assertEquals($actual_qty,$expected_qty,'Incorrect Quantity after update');
    }

   /**
     * @Given I remove the product :product_name
     */
    public function iRemoveTheProduct($product_name)
    {
       $product_row = $this->getProductRow($product_name);
       $product_row->clickLink("Delete");
    }

   /**
     * @Then I :action the quantity of ":product" by :quantity
     */
    public function iTheQuantityOfBy($action, $product_name, $quantity)
    {
        $product_row = $this->getProductRow($product_name);
        $product_qty = $product_row->find('css','.cart_quantity > input')->getAttribute('value');

        switch ($action)
        {
          case "increase":
            for($index=0;$index < $quantity;$index++)
            {
                $product_row->clickLink("Add");
            }
            break;

          case "decrease":
            if($product_qty >= $quantity)
            {
                for($index=0;$index < $quantity;$index++)
                {
                    $product_row->clickLink("Subtract");
                }
            }
            break;
          default: echo "Incorrect Action" ;
        }
    }

    /**
     * @Then I should not see :product_name product
     */
    public function iShouldNotSeeProduct($product_name)
    {
        $page=$this->getSession()->getPage();
        $product_row = $page->find('xpath','//a[text()="'.$product_name.'"]');

        Assert.assertNull($product_row,'Product not removed successfully');
    }

    /**
     * @Given I click on Checkout
     */
    public function iClickOnCheckout()
    {
        $page = $this->getSession()->getPage();
        $page->find('css','.cart_navigation')->clickLink("Proceed to checkout");
    }

    /**
     * @When I wait for the page to be loaded
     */
    public function iWaitForThePageToBeLoaded()
    {
      #$this->getSession()->wait(20000, "document.readyState === 'complete'");
      sleep(10);
    }

}
