@javascript
Feature: This test case will write some dummy data into the fields that were changed by other tests

  Scenario: Write dummy data to the fields used in the tests before
    Given I am on "/user-local"
    And I log in to main site
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/contact_us_form/"
    And I write "adasdasdasdssa" into "cke_edit-syn-contact-contact-us-description-value" wysiwyg
    And I write "1313123123123123" into "cke_edit-syn-contact-contact-us-privacy-link-value" wysiwyg
    And I push the "Submit" button
    #Change Copyright block text
    Then I go to "/admin/config/syngenta/copyright"
    And I fill in "edit-syn-panels-copyright" with "asdasdasad1231231"
    And I push the "Submit" button
    #Change Geography Indicator text
    Then I go to "/admin/config/syngenta/geography-indicator/"
    And I fill in "edit-syn-country-core-geography-indicator" with "adasdasdad1321dsada23"
    And I push the "Submit" button
