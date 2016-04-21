@javascript
Feature: This test case will check the "Privacy Policy" setting

  Scenario: Change the settings for Privacy Policy
    #Secure logout
    And I go to "/user/logout/"
    Given I am on "/user-local"
    And I log in to main site
    Then I go to "/admin/config/syngenta/customer/"
    #Open the Privacy Policy tab
    And I click on the text "Privacy Policy"
    And I fill in "edit-syn-customer-privacy-title" with "Privacy policy Big K 2016"
    And I fill in "edit-syn-customer-privacy-checkbox-text" with "I accept soundcloud etc"
    And I push the "Submit" button
    And I end the session

  Scenario: Check registration page as anonymous
    And I go to "/user/register/"
    And I reload the page
    Then I should see "Privacy policy Big K 2016 "
    And I should see "I accept soundcloud etc "
