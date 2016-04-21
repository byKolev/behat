@javascript
Feature: This test case will write some dummy data into the fields that were changed by other tests

  Scenario: Write dummy data to the fields  used in the tests before
    Given I am on "/user-local"
    And I log in to main site
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/text-blocks/"
    And I click on the text "Show" in the "TextBlocksProductCP" region
    And I write "9998888" into "cke_edit-syn-editorial-text-blocks-syn-product-cp-product-cp-search-value" wysiwyg
    And I push the "Submit" button
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/text-blocks/"
    And I click on the text "Show" in the "TextBlocksSeed" region
    And I write "121212" into "cke_edit-syn-editorial-text-blocks-syn-product-seed-product-seed-search-value" wysiwyg
    And I push the "Submit" button
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/text-blocks/"
    And I click on the text "Show" in the "TextBlocksTarget" region
    And I write "31313131" into "cke_edit-syn-editorial-text-blocks-syn-product-target-product-target-search-value" wysiwyg
    And I push the "Submit" button
