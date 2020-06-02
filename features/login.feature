@javascript
Feature: Login to the site

  Scenario Outline: Verify an authenticated user can login to the site successfully
    Given I am on the homepage
    And I click "Sign in"
    When I fill in "email" with "<email>"
    And I fill in "passwd" with "<password>"
    And I press "Sign in"
    Then I should see "<output>"

Examples:
    | email                  | password   | output     |
    | behat_demo@getnada.com | Test@12345 | My account |
    | invalid_fail@gamil.com | Fail@123   | Authentication failed. |