@javascript
Feature: This test case will check whether the anonymous users sees the help texts on the 4 search pages

  Scenario: Check the help texts on different search pages
    #Product: Crop Protection search
    And I go to home page using "HTTP" protocol
    Then I go to "/products/search/crop-protection"
    And I reload the page
    And I should see the text "Product: Crop Protection custom by Big K 2016"
    #Product: Seed search
    Then I go to "/products/search/seed"
    And I reload the page
    And I should see the text "Product: Seed custom by Big K 2016"
    #Product: Target search
    Then I go to "/search/target"
    And I reload the page
    And I should see the text "Product: Target custom by Big K 2016"
    #Global search
    Then I go to "/search"
    And I reload the page
    And I should see the text "Global Search Big K 2016"
