@javascript
Feature: This test case will verify the ability of the Administrator to set "Text blocks" on "Customer settings"

  Scenario: Change the Text blocks settings on the Customer settings page
    Given I am on "/user-local"
    And I log in to main site
    #Change the Text blocks settings on Customer settings page
    Then I go to "/admin/config/syngenta/customer"
    #Change Customer help: My Profile (before subtabs for all profiles except preferences)
    And I write "Tiesto is coming to Bulgaria" into "cke_edit-tbmyprofile-value" wysiwyg
    #Change Customer help: My Preferences (before subtabs)
    And I write "And Armin van Buuren is coming to Bulgaria" into "cke_edit-tbmypreferences-value" wysiwyg
    #Change Customer help: Registration
    And I write "Leave the world behind ya" into "cke_edit-tbregistration-value" wysiwyg
    #Change Customer help: User dashboard
    And I write "Karachi" into "cke_edit-tbuserdashboard-value" wysiwyg
    #Change Customer help button: User dashboard
    And I write "iPhone" into "cke_edit-tbuserhelp-value" wysiwyg
    #Change Customer help: My Profile (after subtabs)
    And I write "Gshock" into "cke_edit-tbmyprofileundersubtabs-value" wysiwyg
    #Change Customer help: Account settings (after subtabs)
    And I write "Casio" into "cke_edit-tbaccountundersubtabs-value" wysiwyg
    #Change Customer help: My Company (after subtabs)
    And I write "ClubLife" into "cke_edit-tbmycompanyundersubtabs-value" wysiwyg
    #Change Customer help: My Farm (after subtabs)
    And I write "iPad" into "cke_edit-tbmyfarmundersubtabs-value" wysiwyg
    #Change Customer help: Submit button help message
    And I write "Acquia" into "cke_edit-tbsubmithelp-value" wysiwyg
    And I push the "Submit" button
    #Check the text blocks as Administrator
    Then I go to "/user"
    And I reload the page
    And I maximize window
    #Check the texts on "Account settings"
    And I click on the text "My Profile"
    And I click on the text "Account settings" in the "MyProfileSecondaryTabs" region
    #And I click on the text "Account settings"
    Then I should see "Karachi"
    And I should see "iPhone"
    And I should see "Tiesto is coming to Bulgaria"
    And I should see "Casio"
    And I should see "Acquia"
    #Check the texts on My Profile
    And I click on the text "My Profile"
    Then I should see "Karachi"
    And I should see "iPhone"
    And I should see "Tiesto is coming to Bulgaria"
    And I should see "Gshock"
    And I should see "Acquia"
    #Check the texts on "My Company"
    And I click on the text "My Company"
    Then I should see "Karachi"
    And I should see "iPhone"
    And I should see "Tiesto is coming to Bulgaria"
    And I should see "ClubLife"
    And I should see "Acquia"
    #Check the texts on "My farm"
    And I click on the text "My farm" in the "MyProfileSecondaryTabs" region
    Then I should see "Karachi"
    And I should see "iPhone"
    And I should see "Tiesto is coming to Bulgaria"
    And I should see "iPad"
    And I should see "Acquia"
    #Check the texts on "My Preferences"
    And I click on the text "My preferences"
    Then I should see "Karachi"
    And I should see "iPhone"
    And I should see "And Armin van Buuren is coming to Bulgaria"
    And I should see "Acquia"
    #Check the texts on "My Services"
    And I click on the text "My services"
    Then I should see "Acquia"
