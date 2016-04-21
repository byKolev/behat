@javascript
Feature: This test case will test the ability of the Administrator to change the description text on the Search page

  Scenario: Change the description text on the Search page
    Given I am on "/user-local"
    And I log in to main site
    #Change description for Contact Us form
    Then I go to "/admin/config/syngenta/text-blocks/"
    And I click on the text "Show" in the "TextBlocksSearch" region
    And I write "Global Search Big K 2016" into "cke_edit-syn-editorial-text-blocks-syn-search-global-search-value" wysiwyg
    And I push the "Submit" button
    Then I go to "/search"
    And I reload the page
    And I should see the text "Global Search Big K 2016"
