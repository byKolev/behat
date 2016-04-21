@javascript
Feature: This test case will test the ability of the Administrator to change the geography indicator text.

  Scenario: Change the geography indicator text
    Given I am on "/user-local"
    And I log in to main site
    #Change Geography Indicator text
    Then I go to "/admin/config/syngenta/geography-indicator/"
    And I fill in "edit-syn-country-core-geography-indicator" with "Cayman Islands"
    And I push the "Submit" button
    Then I go to "/"
    And I reload the page
    And I should see the text "Cayman Islands"
