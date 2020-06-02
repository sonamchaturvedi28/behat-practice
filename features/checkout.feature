@javascript
Feature: Checkout a product

  @checkout
  Scenario: Verify user is able to purchase a product
    Given I am on the homepage
    And I follow "Women"
    And I add "Printed Chiffon Dress" into the cart
    And I press "Submit"
    Then I should see "Product successfully added to your shopping cart"
    And I follow "Proceed to checkout"
    And I click on Checkout
    And I should see "Authentication"
    When I fill in the following:
       | email  | behat_demo@getnada.com|
       | passwd | Test@12345            |
    And I press "Sign in"
    Then I should see "ADDRESSES"
    And I should see "Charlotte"
    And I press "processAddress"
    Then I should see "Shipping"
    And I check "I agree to the terms of service"
    And I press "processCarrier"
    Then I should see "PLEASE CHOOSE YOUR PAYMENT METHOD"
    And I should see correct total price
    And I follow "Pay by check."
    Then I should see "CHECK PAYMENT"
    And I press "I confirm my order"
    Then I should see "Your order on My Store is complete."