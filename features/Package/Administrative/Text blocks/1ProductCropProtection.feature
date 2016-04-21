@javascript
Feature: This test case will test the ability of the Administrator to change the description text on the Product: Crop Protection search page

  Scenario: Change the description text on the Product: Crop Protection page
    Given I am on "/user-local"
    And I log in to main site
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/text-blocks/"
    And I click on the text "Show" in the "TextBlocksProductCP" region
    And I write "Product: Crop Protection custom by Big K 2016" into "cke_edit-syn-editorial-text-blocks-syn-product-cp-product-cp-search-value" wysiwyg
    And I push the "Submit" button
    Then I go to "/products/search/crop-protection"
    And I reload the page
    And I should see the text "Product: Crop Protection custom by Big K 2016"
