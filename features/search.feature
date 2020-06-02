@javascript
Feature: Search on the site

@search
  Scenario Outline: Verify a user can search on the site successfully
    Given I am on the homepage
    When I fill in "search_query" with "<search>"
    And I press "submit_search"
    Then I should see "results have been found"

    Examples:
    | search |
    | dress  |
    | jeans  |
    | women  |