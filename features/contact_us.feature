@javascript
Feature: Contact Us page

  Scenario: Verify a user can submit Contact Us form successfully
    Given I am on the homepage
    And I click "Contact us"
    When I select "Customer service" from "Subject Heading"
    And I fill in "Email address" with "behat_demo@getnada.com"
    And I fill in "id_order" with "Order#123"
    And I fill in "Message" with "Message testing"
    And I press "Send"
    Then I should see "Your message has been successfully sent to our team."