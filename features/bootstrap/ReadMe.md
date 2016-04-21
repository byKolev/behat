
**Deafault Steps**

-
	And I follow "link-id"

To click on a link, selecting it by an ID


-
	And I follow "Link Title"

To click on a link, selecting it by the text in the link


-
	And I fill in "field-id" with "text"

To type in a text field


-
	And I select "option" from "select-id"

To select an option from a select


**Behat custom steps definitions**
=

_This document contains information about all custom Behat steps that we have created._


**Toni**
-
    I log in as "username" "password"

You are supposed to be on the login page. This step fills in the **username** and **password** fields with the provided credentials and presses the **Login** button.

    I go to home page using "<protocol>" protocol

This step allows you to visit the homepage of the profile either using **HTTP** or **HTTPS** protocol.

    I scroll element with id "id" to the top

This step scrolls your browser and positions the element with the provided **id** to the top of the browser browser window.

    I scroll element with class "class" to the top

This step scrolls your browser and positions the first element with the provided **class** to the top of the browser window.

    the "option" option from "select" (is|should be) selected
    the option "option" from "select" (is|should be) selected
    "option" from "select" (is|should be) selected

Checks whether the provided "option" is selected in the provided "select" field.

    I reload the current page without GET parameters

Reloads the current page removing the GET parameters.

    And I should see "text" in the source of the page

Searches the provided "text" in the source of the page.

**Georgi**
-
    I select the first autocomplete option for "string" on the "id" field

This step allows you to select the first autocomplete result for provided **string** on the specified **field**.

    I maximize window

Maximizes the browser window.

    I check for error messages

This step checks for errors/notices/warnings on the current page and throws an exception if any found.

    I should see the text "text" once

This step checks whether there is only one occurrence of the provided "text"

**Daniel**
-
    I stop the session
    I end the session

This step stops the session.

    I check "element" if not checked yet

This step step checks the **element** checkbox if it's not yet checked.

    I uncheck "element" if checked already

This step step unchecks the **element** checkbox if it's already checked.
