@javascript
Feature: This test case will test the ability of the Administrator to change the description text on the Contact Us form

  Scenario: Change the description text on Contact Us form
    Given I am on "/user-local"
    And I log in to main site
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/contact_us_form/"
    And I write "Contact Us Description Big K 2016" into "cke_edit-syn-contact-contact-us-description-value" wysiwyg
    And I write "Privacy Policy Text And Link" into "cke_edit-syn-contact-contact-us-privacy-link-value" wysiwyg
    And I push the "Submit" button
    Then I go to "/contact-us"
    And I reload the page
    And I click on the text "Contact Us" in the "ContactUsLinks" region
    And I should see the text "Contact Us Description Big K 2016"
    And I should see "Privacy Policy Text And Link"
