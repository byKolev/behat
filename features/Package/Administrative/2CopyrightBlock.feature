@javascript
Feature: This test case will test the ability of the Administrator to change the copyright block content

  Scenario: Change the copyright block content
    Given I am on "/user-local"
    And I log in to main site
    #Change Copyright block text
    Then I go to "/admin/config/syngenta/copyright"
    And I fill in "edit-syn-panels-copyright" with "Rozay Copyright Block 2016"
    And I push the "Submit" button
    Then I go to "/"
    And I reload the page
    And I should see the text "Rozay Copyright Block 2016"
