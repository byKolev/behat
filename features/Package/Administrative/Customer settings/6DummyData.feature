@javascript
Feature: This test case will write some dummy data into the fields that were changed by other tests

  Scenario: Write dummy data to the fields  used in the tests before
    Given I am on "/user-local"
    And I log in to main site
    #Change the Text blocks settings on Customer settings page
    Then I go to "/admin/config/syngenta/customer"
    #Change Customer help: My Profile (before subtabs for all profiles except preferences)
    And I write "00000000000000000000" into "cke_edit-tbmyprofile-value" wysiwyg
    #Change Customer help: My Preferences (before subtabs)
    And I write "9999999999999999" into "cke_edit-tbmypreferences-value" wysiwyg
    #Change Customer help: Registration
    And I write "888888888888888" into "cke_edit-tbregistration-value" wysiwyg
    #Change Customer help: User dashboard
    And I write "77777777777777" into "cke_edit-tbuserdashboard-value" wysiwyg
    #Change Customer help button: User dashboard
    And I write "66666666666666" into "cke_edit-tbuserhelp-value" wysiwyg
    #Change Customer help: My Profile (after subtabs)
    And I write "5555555555555" into "cke_edit-tbmyprofileundersubtabs-value" wysiwyg
    #Change Customer help: Account settings (after subtabs)
    And I write "444444444444444" into "cke_edit-tbaccountundersubtabs-value" wysiwyg
    #Change Customer help: My Company (after subtabs)
    And I write "11111111111111" into "cke_edit-tbmycompanyundersubtabs-value" wysiwyg
    #Change Customer help: My Farm (after subtabs)
    And I write "22222222222222" into "cke_edit-tbmyfarmundersubtabs-value" wysiwyg
    #Change Customer help: Submit button help message
    And I write "333333333333333" into "cke_edit-tbsubmithelp-value" wysiwyg
    And I push the "Submit" button
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/customer"
    And I click on the text "Registration form"
    And I write "1111111111111111" into "cke_edit-syn-customer-help-registration-teaser-value" wysiwyg
    And I write "222222222222" into "cke_edit-syn-customer-help-registration-value" wysiwyg
    And I push the "Submit" button
    #Open the Terms and Conditions tab
    And I click on the text "Terms and Conditions"
    And I fill in "edit-syn-customer-tc-title" with "11111111111111111"
    And I fill in "edit-syn-customer-tc-checkbox-text" with "22222222222222"
    And I push the "Submit" button
    #Open the Privacy Policy tab
    And I click on the text "Privacy Policy"
    And I fill in "edit-syn-customer-privacy-title" with "33333333333333333"
    And I fill in "edit-syn-customer-privacy-checkbox-text" with "4444444444444"
    And I push the "Submit" button
