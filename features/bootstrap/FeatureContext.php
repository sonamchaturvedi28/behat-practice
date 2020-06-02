<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\TableNode;

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
        $product_add = $page->findAll('css','.product_img_link');
        for($index=0; $index < count($product_add);$index++)
        {
            if(strpos($product_add[$index]->getAttribute('title'),$product_name)!==FALSE)
            {
                $product_add[$index]->click();
            }
        }
    }
    
    /* Get product row based on Product Name
     */
    public function getProductRow($product_name)
    {
        $page = $this->getSession()->getPage();
        
        $products = $page->findAll('css', '.cart_item');
        if (empty($products)) {
         throw new \Exception(sprintf('No rows found on the page %s', $this->getSession()->getCurrentUrl()));
        }
        for($row=0;$row < count($products);$row++)
        {
            if (strpos($products[$row]->find('css', 'p.product-name')->getText(), $product_name) !== FALSE) {
                return $products[$row];
            }
            /* Alternative code to find product row
            if (strpos($products[$row]->getText(), $product_name) !== FALSE) {
             return $product[$row];
            }*/
        }
    } 
    
    /**
     * @Then I should see correct total price
     */
    public function iShouldSeeCorrectTotalPrice()
    {
       $page = $this->getSession()->getPage();
       $actual_total = 0;
       $product_total = $page->findAll('css','td.cart_total');
       for($index=0;$index < count($product_total);$index++)
       {
           $actual_total = $actual_total + (float)str_replace("$", "",$product_total[$index]->getText());
       }

       $total_shipping = (float)str_replace("$", "",$page->find('css','#total_shipping')->getText());
       
       //Total tax field is available only on Shopping Cart summary page
       if($page->hasContent("Shopping-cart summary"))
       {
           $total_tax = (float)str_replace("$", "",$page->find('css','#total_tax')->getText());
       }
       $actual_cart_total = (float)str_replace("$", "",$page->find('css','#total_price_container')->getText());
       $expected_cart_total = $actual_total + $total_shipping + $total_tax;
       if ((string)$actual_cart_total !== (string)$expected_cart_total) {
            throw new \Exception('Incorrect Total Cart Price result');
        }
    }
    
    /**
     * @Then I should see Product details :
     */
    public function iShouldSeeProductDetails(TableNode $table)
    {
      $array = $table->getRowsHash();
   
      for ($index=0;$index<count($array['Product Name']);$index++)
      {
        $row = $this->getProductRow($array['Product Name']);
        
        $color_size = $row->find('css', '.cart_description > small > a')->getText();
        $color_array = explode(", ", $color_size);
      
        if(strcmp($color_array[0],$array['Product Colour'])!= 0)
        {
            throw new \Exception('Product colour incorrect');
        }       
        if(strcmp($color_array[1],$array['Product Size'])!= 0)
        {
            throw new \Exception('Product size incorrect');
        }
        if(strcmp($row->find('css','.cart_unit > span > span')->getText(), $array['Product Price'])!=0)
        {
             throw new \Exception('Incorrect Unit Price of '.$array['Product Name']);
        }
        if(strcmp($row->find('css','.cart_quantity > input')->getAttribute('value'), $array['Product Quantity'])!=0)
        {
             throw new \Exception('Incorrect Quantity of '.$array['Product Name']);
        }
      } 
    }
    
    /**
     * @Then I should see quantity of :product_name as :expected_qty
     */
    public function iShouldSeeQuantityOfAs($product_name, $expected_qty)
    {
        $row = $this->getProductRow($product_name);
        $actual_qty = $row->find('css','.cart_quantity > input')->getAttribute('value');
        if((string)$actual_qty!==(string)$expected_qty)
        {
             throw new \Exception('Incorrect Quantity after update');
        }
    }
    
   /**
     * @Given I remove the product :product_name
     */
    public function iRemoveTheProduct($product_name)
    {
       $row = $this->getProductRow($product_name);
       $row->clickLink("Delete");
    }
    
   /**
     * @Then I :action the quantity of ":product" by :quantity
     */
    public function iTheQuantityOfBy($action, $product_name, $quantity)
    {
        $row = $this->getProductRow($product_name);
        $product_qty = $row->find('css','.cart_quantity > input')->getAttribute('value');
        if($action == "increase")
        {      
            for($index=0;$index < $quantity;$index++)
            {
                $row->clickLink("Add");
            }
        }
        else
        {
            if($product_qty >= $quantity)
            {
                for($index=0;$index < $quantity;$index++)
                {
                    $row->clickLink("Subtract");
                }
            }
        }
        $this->getSession()->reload();
    }
    
    
    /**
     * @Then I should not see :product_name product
     */
    public function iShouldNotSeeProduct($product_name)
    {
        $page=$this->getSession()->getPage();
        $products = $page->findAll('css', 'td.cart_item');
        
        //Checking if atleast one product in the cart after deleting other product
        if(count($products) >= 1)
        {
            for($row=0;$row < count($products);$row++)
            {
                if(strpos($products[$row]->find('css', 'p.product-name')->getText(),$product_name) !== FALSE)
                 {
                     throw new \Exception('Product not removed successfully');
                 }
            }
        }
    }
    
    /**
     * @Given I click on Checkout
     */
    public function iClickOnCheckout()
    {
        $page = $this->getSession()->getPage();
        $page->find('css','.cart_navigation')->clickLink("Proceed to checkout");
    }
}