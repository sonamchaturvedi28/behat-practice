@javascript
Feature: Add to Cart Feature

Background: To add a product in the cart
    Given I am on the homepage
    And I follow "Women"
    And I follow "Faded Short Sleeve T-shirts"
    And I fill in "quantity_wanted" with "2"
    And I select "M" from "Size"
    And I press "Submit"
    Then I should see "Product successfully added to your shopping cart"
    And I follow "Proceed to checkout"

  @add
  Scenario: Verify user is able to add another product into the cart
    Given I am on the homepage
    And I follow "Women"
    And I add "Printed Chiffon Dress" into the cart
    And I press "Submit"
    Then I should see "Product successfully added to your shopping cart"

  @view
  Scenario: Verify user is able to view product details in the Cart
    Given I am on the cart page
    And I should see "SHOPPING-CART SUMMARY"
    And I should see Product Details :
    |Product Name         |Faded Short Sleeve T-shirts|
    |Product Colour       |Color : Orange             |
    |Product Size         |Size : M                   |
    |Product Price        |$16.51                     |
    |Product Quantity     |2                          |
    And I should see correct total price

  @update
  Scenario: Verify user is able update the product quantity in the shopping cart
   Given I am on the cart page
   And I "increase" the quantity of "Faded Short Sleeve T-shirts" by "1"
   Then I should see quantity of "Faded Short Sleeve T-shirts" as "3"
   And I should see correct total price

  @remove
  Scenario: Verify user is able to remove a product from the cart
   Given I am on the cart page
   And I remove the product "Faded Short Sleeve T-shirts"
   And I wait for AJAX to finish
   Then I should not see "Faded Short Sleeve T-shirts" product