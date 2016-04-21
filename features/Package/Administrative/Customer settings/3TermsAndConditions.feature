@javascript
Feature: This test case will check the "Terms and Conditions" setting

  Scenario: Change the settings for Terms and Conditions
    #Secure logout
    And I go to "/user/logout/"
    Given I am on "/user-local"
    And I log in to main site
    Then I go to "/admin/config/syngenta/customer/"
    #Open the Terms and Conditions tab
    And I click on the text "Terms and Conditions"
    And I fill in "edit-syn-customer-tc-title" with "Terms and Conditions BigK 2016"
    And I fill in "edit-syn-customer-tc-checkbox-text" with "I accept powpowGT etc"
    And I push the "Submit" button
    And I end the session

  Scenario: Check registration page as anonymous
    And I go to "/user/register/"
    And I reload the page
    Then I should see "Terms and Conditions BigK 2016"
    And I should see "I accept powpowGT etc"
