@javascript
Feature: This test case will test the ability of the Administrator to change the Teaser text and Help block text on the Registration page

  Scenario: Change the teaser text and help block text on the Registration page
    Given I am on "/user-local"
    And I log in to main site
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/customer"
    And I click on the text "Registration form"
    And I write "Teaser Text Custom on Registration page by Big K 2016" into "cke_edit-syn-customer-help-registration-teaser-value" wysiwyg
    And I write "Help block text on Registration page by Big K 2016" into "cke_edit-syn-customer-help-registration-value" wysiwyg
    And I push the "Submit" button
    And I end the session

  Scenario: Check registration page as anonymous
    And I go to "/user/register/"
    And I reload the page
    Then I should see "Teaser Text Custom on Registration page by Big K 2016"
    And I click on the text "Need help?"
    And I should see "Help block text on Registration page by Big K 2016"
