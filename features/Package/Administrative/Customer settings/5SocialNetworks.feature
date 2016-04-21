@javascript
Feature: This test case will check the "Social networks" setting

  Scenario: Change the settings for Social netowrks registration
    #Secure logout
    And I go to "/user/logout/"
    Given I am on "/user-local"
    And I log in to main site
    Then I go to "/admin/config/syngenta/customer/"
    #Open the Social networks tab
    And I click on the text "Social networks"
    And I check the box "edit-syn-customer-social-logins-enabled-facebook"
    And I check the box "edit-syn-customer-social-logins-enabled-google"
    And I uncheck the box "edit-syn-customer-social-logins-enabled-linkedin"
    And I uncheck the box "edit-syn-customer-social-logins-enabled-twitter"
    And I push the "Submit" button
    And I end the session

  Scenario: Check registration page as anonymous
    And I go to "/user/register/"
    Then I should see "Facebook"
    And I should see "Google"
    And I should not see "Twitter"
    And I should not see "LinkedIn"
