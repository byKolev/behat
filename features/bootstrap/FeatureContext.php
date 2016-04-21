<?php

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
* Features context.
*
* @author Bozhidar Boshnakov <bboshnakov91@gmail.com> and awesome QA Team
*/
class FeatureContext extends Drupal\DrupalExtension\Context\DrupalContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   * Every scenario gets its own context object.
   *
   * @param array $parameters context parameters (set them up through behat.yml)
   */
  /*public function __construct(array $parameters)
  {
      // Initialize your context here
      $this->useContext('wysiwyg', new WysiwygSubContext());
  }*/
  private $params = array();

  public function __construct(array $parameters) {

    $this->params = $parameters;
  }

  /** 
  * Push a Submit, Delete, Run etc. button.
  *
  * @Given /^I push the "([^"]+)" button$/
  */
  public function iPushTheButton($button) {

    $buttons = array(
        'Submit' => 'edit-submit',
        'Delete' => 'edit-delete',
        'Run' => 'edit-run',
        'Cancel' => 'edit-cancel',
        'Deploy' => 'edit-deploy',
        'Apply' => 'edit-submit-admin-views-user',
        'Index50' => 'edit-cron',
        'SubmitFilters' => 'edit-filters-submit',
        'SubmitPayment' => 'btnAuthSubmit',
        'Confirm' => 'edit-confirm',
        'Recipe' => 'btn_continue',
        'Finish' => 'edit-return',
        'Save' => 'panels-ipe-save'
    );

    if (!isset($buttons[$button])) {
      throw new InvalidArgumentException(sprintf('"%s" button is not mapped. Map the button in your function.', $button));
    }

    $this->getSession()->getPage()->pressButton($buttons[$button]);
  }
    
  /** 
  * Syngenta specific function
  *
  * @When /^I expose the hidden field "([^"]*)"$/
  */
  public function iRemoveJS($address) {

    $function = <<<EOS
    (function($) {

      var divTag = document.getElementById("$address");
      divTag.style.display = "block";
    })(jQuery);
EOS;

    try {
      $this->getSession()->executeScript($function);
    }
    catch(Exception $e) {
      throw new \Exception("JS failed miserably");
    }
  }

  /**
  * Waiting for text to appear on a page with certain execution time
  *
  * @When /^I wait for text "([^"]*)" to appear with max time "([^"]+)"(?: seconds)?$/
  */
  public function iWaitForTextToAppearWithMaxTime($text, $maxExecutionTime) {
    
    $isTextFound = false;

    for ($i = 0; $i < $maxExecutionTime; $i++) {
      try {
        $this->assertPageContainsText($text);
        $isTextFound = true;
        break;
      }
      catch (Exception $e) {
        sleep(1);
      }
    }

    if (!$isTextFound) {
      throw new Exception("'$text' didn't appear on the page for $maxExecutionTime seconds");
    }
  }

  /**
  * Click on some text.
  *
  * @When /^I click on the text "([^"]*)"$/
  */
  public function iClickOnTheText($text) {

    $session = $this->getSession();
    $element = $session->getPage()->find(
        'xpath',
        $session->getSelectorsHandler()->selectorToXpath('xpath', '//*[contains(text(),"' . $text . '")]'));

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
    }

    $element->click();
  }

  /** 
  * Confirms the currently opened popup.
  *
  * @When /^(?:|I )confirm the popup$/
  */
  public function confirmPopup() {

    $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
  }

  /** 
  * Cancels the currently opened popup.
  *
  * @When /^(?:|I )cancel the popup$/
  */
  public function cancelPopup() {

    $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
  }


  /** 
  * Click on the element with the provided xpath query.
  *
  * @When /^I click on the element with xpath "([^"]*)"$/
  */
  public function iClickOnTheElementWithXPath($xpath) {

    $session = $this->getSession();
    $element = $session->getPage()->find(
        'xpath',
        $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
    );

    if ($element === null) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s" for xpath: "%g"', $element, $xpath));
    }
    // ok, let's click on it
    $element->click();
  }

  /** 
  * Set value to the element with the provided xpath query.
  *
  * @When /^I set value "([^"]*)" to the element with xpath "([^"]*)"$/
  */
  public function iSetValueToTheElementWithXPath($value,$xpath) {

    $session = $this->getSession();
    $element = $session->getPage()->find(
        'xpath',
        $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
    );

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $element));
    }

    $element->setValue($value);
  }

  /**
  * Click on the element with the provided CSS Selector
  *
  * @When /^I click on the element with css selector "([^"]*)"$/
  */
  public function iClickOnTheElementWithCSSSelector($cssSelector) {

    $session = $this->getSession();
    $element = $session->getPage()->find(
        'css',
        $session->getSelectorsHandler()->selectorToXpath('css', $cssSelector)
    );

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS Selector: "%s"', $cssSelector));
    }

    $element->click();
  }

  /** 
  * The browser sleeps for seconds.
  *
  * @Given /^I sleep for "([^"]+)"(?: seconds)?$/
  */
  public function iSleepForSeconds($var) {

    $seconds = ((int)$var);
    sleep($seconds);
  }

  /** 
  * The browser waits for seconds.
  *
  * @Given /^I wait for "([^"]+)"(?: seconds)?$/
  */
  public function iWaitForSeconds($var) {

    $seconds = ((int)$var) * 1000;
    $this->getSession()->wait($seconds);
  }

  /**
  * Opens certain page
  *
  * @Given /^I (?:am on the|go to the) "([^"]+)"(?: page)?$/
  */
  public function iAmOnThe($page) {

    $pages = array(
        'homepage' => '/',
        'login'   => '/user',
        'logout' => '/user/logout',
        'register' => '/user/register',
        'contact' => '/contact',
        'blog' => '/blog',
        'search' => '/search/site',
        'add article' => '/node/add/article',
        'add place' => 'node/add/place',
        'add institution' => 'node/add/institution',
        'content' => 'admin/content',
        'cron' => 'admin/config/system/cron'
    );

    if (!isset($pages[$page])) {
      throw new InvalidArgumentException(sprintf('"%s" page is not mapped. Map the page in your function.', $page));
    }

    $this->getSession()->visit($this->locatePath($pages[$page]));
  }

  /** 
  * Log in Drupal with provided username and password.
  *
  * @Given /^I log in as "([^"]+)" "([^"]+)"$/
  */
  public function iLogInAs($username, $password) {

    $session = $this->getSession();
    $page = $session->getPage();
    $page->fillField("edit-name", $username);
    $page->fillField("edit-pass", $password);
    $page->pressButton("edit-submit");
  }

  /** 
  * Pause the test and wait for user iteration before continuing.
  *
  * @Then /^(?:|I )put a breakpoint$/
  */
  public function breakpoint() {

    fwrite(STDOUT, "\033[s \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
    while (fgets(STDIN, 1024) == '') {
    }

    fwrite(STDOUT, "\033[u");

    return;
  }

  /** 
  * Go to specific LUSH login page using parameters
  *
  * @Given /^I go to LUSH login page$/
  */
  public function iGoToLUSHloginPage() {

    $session = $this->getSession();
    $page = $session->getPage();
    $loginPath = $this->params[2];
    $this->getSession()->visit($this->locatePath($loginPath));
  }

  /** 
  * Visit profile's homepage with either HTTP or HTTPS
  *
  * @Given /^I go to home page using "([^"]*)" protocol$/
  */
  public function iGoHomePageThrough($protocol) {

    $base_url = $this->getMinkParameter('base_url');

    if ($protocol == 'HTTP') {
      if ($base_url[4] == ':') {
        $this->getSession()->visit($base_url);
      }
      else {
        $string = $base_url;
        $pattern = '/https/';
        $replacement = 'http';
        $urlToVisit = preg_replace($pattern, $replacement, $string);
        $this->getSession()->visit($urlToVisit);
      }
    }
    else if ($protocol == 'HTTPS') {
      if ($base_url[4] == 's') {
        $this->getSession()->visit($base_url);
      }
      else {
        $string2 = $base_url;
        $pattern2 = '/http/';
        $replacement2 = 'https';
        $urlToVisit2 = preg_replace($pattern2, $replacement2, $string2);
        $this->getSession()->visit($urlToVisit2);
      }
    }
    else {
      throw new Exception('You are supposed to select HTTP or HTTPS as a protocol.');
    }
  }

  public function findElementWith($input) {

    $extractedArgument = substr($input, 1, strlen($input) - 1);

    if ($input[0] === "."){
      return "class";
    }

    if ($input[0] === "#"){
      return "id";
    }

    return "text";
  }

  /**
  * Scrolling element with id|class|text to the top of the page
  *
  * @When /^I scroll element with "([^"]*)" to the top$/
  */
  public function iScrollElementWith($argument) {

    $idClass = $this->findElementWith($argument);

    $extractedArgument = substr($argument, 1, strlen($argument) - 1);
    
    $function = <<<JS
    (function(){

      if ("$idClass" === "class") {
        var element = document.getElementsByClassName("$extractedArgument")[0];

        if (element === null){
          throw "Error";
        }

        element.scrollIntoView(true);
      }
      else if ("$idClass" === "id") {
        var element = document.getElementById("$extractedArgument");

        if (element === null){
          throw "Error";
        }

        element.scrollIntoView(true);
      }
      else if ("$idClass" === "text") {
        var aTags = document.getElementsByTagName("label");

        if (aTags === null){
          throw "Error";
        }

        for (var i = 0; i < aTags.length; i++){
          if (aTags[i].textContent.trim() === "$argument"){
            aTags[i].scrollIntoView(true);
            break;
          }
        }
      }
    })()
JS;

    try {
      $this->getSession()->executeScript($function);
    }
    catch(Exception $e) {
      throw new \Exception("Probably I was not able to find an element with this id...actually I don't know what is the problem :(");
    }
  }

  /**
  * Checks, that option from select with specified id|name|label|value is selected.
  *
  * @Then /^the "(?P<option>(?:[^"]|\\")*)" option from "(?P<select>(?:[^"]|\\")*)" (?:is|should be) selected/
  * @Then /^the option "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)" (?:is|should be) selected$/
  * @Then /^"(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)" (?:is|should be) selected$/
  */
  public function theOptionFromShouldBeSelected($option, $select) {

    $selectField = $this->getSession()->getPage()->findField($select);

    if (null === $selectField) {
      throw new ElementNotFoundException($this->getSession(), 'select field', 'id|name|label|value', $select);
    }

    $optionField = $selectField->find('named', array(
        'option',
        $option,
    ));

    if (null === $optionField) {
      throw new ElementNotFoundException($this->getSession(), 'select option field', 'id|name|label|value', $option);
    }

    if (!$optionField->isSelected()) {
      throw new Exception('Select option field with value|text "' . $option . '" is not selected in the select "'.$select.'"');
    }
  }

  public function validateTextForSearchInSource($text) {

    $text = preg_replace("/(\\.)/", '\\\\.', $text);
    $text = preg_replace("/(\/)/", '\\/', $text);
    $text = preg_replace("/(\\?)/", '\\\\\?', $text);

    return $text;
  }

  /** 
  * Checks the source of the current page for provided text.
  *
  * @When I should see :text in the source of the page
  */
  public function iShouldSeeInTheSourceOfThePage($text) {

    $html = $this->getSession()->getDriver()->getContent();
    $text = $this->validateTextForSearchInSource($text);
    $regex = '/' . $text . '/';

    preg_match($regex, $html, $results);

    if ($results == null) {
      throw new Exception('The searched text ' . $text . ' was not found in the source of the page.');
    }

    return true;
  }

  /** 
  * Checks the checkbox that selects all results in the result table of the view
  *
  * @When I select all results
  */
  public function iSelectAllResults() {

    $function = <<<JS
    (function($){

      var checkboxes = document.getElementsByClassName("vbo-table-select-all");
      checkboxes[1].id = "checkThemAll";
    })(jQuery);
JS;

    $this->getSession()->executeScript($function);
    $Page = $this->getSession()->getPage();
    $Page->checkField("checkThemAll");
  }

  /** 
  * Save the one-time login URL to a file in order to visit it later
  *
  * @When I save the one-time login URL
  */
  public function iSaveTheOneTimeLoginURL() {

    $url = $this->getSession()->getCurrentUrl();
    file_put_contents("otl.txt", $url);
  }

  /** 
  * Visit the saved one-time login URL from the file
  *
  * @When I visit the one-time login URL
  */
  public function iVisitTheOneTimeLoginURL() {

    $file = fopen("otl.txt", "r");
    $url = fread($file, 100000);
    $this->getSession()->visit($url);
  }

// --------------BOZHIDAR-------------------

  /**
  * Moves user to the specified path.
  *
  * @Given /^I am in the "([^"]*)" path$/
  *
  * @param   string $path
  */
  public function iAmInThePath($path) {

    $this->moveToNewPath($path);
  }

  /**
  * The browser waits till the page is being loaded
  *
  * @Given /^I wait until the page loads "([^"]*)"$/
  */
  public function iWaitUntilThePageLoads($callback = null) {
    // Manual timeout in seconds
    $timeout = 60;
    // Default callback
    if (empty($callback)) {
      if ($this->getSession()->getDriver() instanceof Behat\Mink\Driver\GoutteDriver) {
        $callback = function($context) {
          // If the page is completely loaded and the footer text is found
          if(200 == $context->getSession()->getDriver()->getStatusCode()) {
            return true;
          }

          return false;
        };
      }
      else {
        // Convert $timeout value to milliseconds
        // document.readyState becomes 'complete' when the page is fully loaded
        $this->getSession()->wait($timeout*1000, "document.readyState == 'complete'");

        return;
      }
    }

    if (!is_callable($callback)) {
      throw new Exception('The given callback is invalid/doesn\'t exist');
    }
    // Try out the callback until $timeout is reached
    for ($i = 0; $i < $timeout/2; $i++) {
      if ($callback($this)) {
        return true;
      }
      // Try every 2 seconds
      sleep(2);
    }

    throw new Exception('The request is timed out');
  }

  /**
  * Switching the perspective to the second tab/window.
  *
  * @Given /^I switch to window "([^"]*)"$/
  */
  public function iSwitchToNewWindow($windowNumber) { 

    $windowNames = $this->getSession()->getWindowNames();

    if(count($windowNames) > $windowNumber) {
      $this->getSession()->switchToWindow($windowNames[$windowNumber]);
    }
    else{
      throw new Exception('You request tab/window number that does not exist');
    }
  }

  /**
  * Switching the perspective to iFrame
  *
  * @Given /^I switch to iframe "([^"]*)"$/
  */
  public function iSwithToIframe($arg1) {

    $originalWindowName = $this->getSession()->getDriver()->getWindowName(); //Get the original name

    if (empty($this->originalWindowName)) {
      $this->originalWindowName = $originalWindowName;
    }

    $this->getSession()->switchToIframe($arg1);
  }

  /**
  * Switching the perspective to popup specific
  *
  * @Then /^I switch to popup specific$/
  */
  public function iSwitchToPopupSpecific() {
    // TODO: Rewrite the expression..
    $originalWindowName = $this->getSession()->getDriver()->getWindowName(); //Get the original name

    if (empty($this->originalWindowName)) {
      $this->originalWindowName = $originalWindowName;
    }

    $this->getSession()->getPage()->pressButton("Withdraw"); //Pressing the withdraw button
    $popupName = $this->getNewPopup($originalWindowName);
    //Switch to the popup Window
    $this->getSession()->switchToWindow($popupName);
  }

  /**
  * Switching the perspective to the original window
  *
  * @Then /^I switch back to original window$/
  */
  public function iSwitchBackToOriginalWindow() {
    //Switch to the original window
    $this->getSession()->switchToWindow($this->originalWindowName);
    $this->getSession()->wait(5000);
  }

  /**
  * Print page information to the console
  *
  * @Then /^I print current window$/
  */
  public function iPrintCurrentWindow() {
    $current_window = $this->getSession()->getDriver()->getWindowName();
    var_dump($current_window);
  }

  /**
  * Find that heading is not in a specified region.
  *
  * @Then /^I should not see the heading "(?P<heading>[^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
  * @Then /^I should not see the "(?P<heading>[^"]*)" heading in the "(?P<region>[^"]*)"(?:| region)$/
  *
  * @throws \Exception
  *   If region or header within it cannot be found.
  */
  public function assertRegionHeading($heading, $region) {

    $regionObj = $this->getRegion($region);

    foreach (array('h1', 'h2', 'h3', 'h4', 'h5', 'h6') as $tag) {
      $elements = $regionObj->findAll('css', $tag);

      if (!empty($elements)) {
        foreach ($elements as $element) {
          if (trim($element->getText()) != $heading) {
            return;
          }
        }
      }
    }

    throw new Exception("There is such heading $heading in that region $region.");
  }

  /** ALREADY EXIST IN THE DEFAULT DrupalContext.php
  * Find that text belongs to a specified region.
  *
  * @Then /^I should see the text "([^"]*)" in the "(?P<region>[^"]*)"(?:| region)l$/
  * @Then /^I should see the "([^"]*)" text in the "(?P<region>[^"]*)"(?:| region)$/
  *
  * @throws \Exception
  *   If region or header within it cannot be found.
  */
  public function assertRegionText($text, $region) {

    $regionObj = $this->getRegion($region);

    foreach (array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'div', 'span', 'p') as $tag) {
      $elements = $regionObj->findAll('css', $tag);
      if (!empty($elements)) {
        foreach ($elements as $element) {
          $tempArray = explode(" ",$element->getText());

          foreach($tempArray as $value) {
            if($value == $text) {
              return;
            }
          }
        }
      }
    }

    throw new Exception("There is no such text $text in that region $region.");
  }
  
  /**
  * Click on text in specified region
  *
  * @When /^I click on the text "([^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
  */
  public function iClickOnTheTextInRegion($text, $region) {

    $session = $this->getSession();
    $element = $session->getPage()->find('region', $region)->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', 
                                                                                                            '//*[contains(text(),"' . $text . '")]'));

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
    }

    $element->click();
  }

  /**
  * Return a region from the current page.
  *
  * @throws \Exception
  *   If region cannot be found.
  *
  * @param string $region
  *   The machine name of the region to return.
  *
  * @return \Behat\Mink\Element\NodeElement
  */
  public function getRegion($region) {

    $session = $this->getSession();
    $regionObj = $session->getPage()->find('region', $region);

    if (!$regionObj) {
      throw new \Exception(sprintf('No region "%s" found on the page %s.', $region, $session->getCurrentUrl()));
    }

    return $regionObj;
  }

  /**
  * Deleting the user
  *
  * @Given /^I delete the "([^"]*)" user$/
  */
  public function iDeleteTheUser($userToDelete) {

    $options = array(
      'yes' => NULL,
      'delete-content' => NULL,
    );

    try {
      $this->getDriver()->drush('user-cancel', array($userToDelete),$options);
    }
    catch (RuntimeException $e) {
      throw new Exception("User with an username is not existing or the drush command user-cancel cannot be executed.");
    }

    $this->getDriver()->processBatch();
  }

  /**
  * Creating user with specified username, role, password, email
  *
  * @Given /^I create user with username "([^"]*)" role "([^"]*)" password "([^"]*)" and email "([^"]*)"$/
  */
  public function iCreateUserWithUsernameRolePasswordAndEmail($user, $role, $password, $email) {

    $options = array(
      'password' => $password,
      'mail' => $email,
    );

    $arguments = array(
      sprintf('"%s"', $role),
      $user,
    );

    try {
      $this->getDriver()->drush('user-create', array($user), $options);
      $this->getDriver()->drush('user-add-role', $arguments);
    }
    catch (RuntimeException $e){
      throw new Exception("The username $user or the email $email are already taken or the drush command user-create cannot be executed.");
    }
  }

  /**
  * Should see text in ceratin element in specified region
  *
  * @Then /^I should see "([^"]*)" in the "([^"]*)" element in the "([^"]*)" region$/
  */
  public function assertRegionElementText($text, $tag, $region) {

    $regionObj = $this->getRegion($region);
    $results = $regionObj->findAll('css', $tag);

    if (!empty($results)) {
      foreach ($results as $result) {
        if ($result->getText() == $text) {
          return;
        }
      }
    }

    throw new \Exception(sprintf('The text "%s" was not found in the "%s" element in the "%s" region on the page %s', $text, $tag, $region, $this->getSession()->getCurrentUrl()));
  }

  /**
  * Should not see text in ceratin element in specified region
  *
  * @Then /^I should not see "([^"]*)" in the "([^"]*)" element in the "([^"]*)" region$/
  */
  public function assertNotRegionElementText($text, $tag, $region) {

    $regionObj = $this->getRegion($region);
    $results = $regionObj->findAll('css', $tag);

    if (!empty($results)) {
      foreach ($results as $result) {
        if ($result->getText() == $text) {
          throw new \Exception(sprintf('The text "%s" was found in the "%s" element in the "%s" region on the page %s', $text, $tag, $region, $this->getSession()->getCurrentUrl()));
        }
      }
    }
  }

  /**
  * Should see image alt in specified region
  *
  * @Then /^I should see the image alt "(?P<link>[^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
  */
  public function assertAltRegion($alt, $region) {

    $regionObj = $this->getRegion($region);
    $element = $regionObj->find('css', 'img');
    $tmp = $element->getAttribute('alt');

    if ($alt == $tmp) {
      $result = $alt;
    }

    if (empty($result)) {
      throw new \Exception(sprintf('No alt text matching "%s" in the "%s" region on the page %s', $alt, $region, $this->getSession()->getCurrentUrl()));
    }
  }

  /**
  * Wait until the Panels IPE is activated.
  *
  * @When /^(?:|I )wait for the Panels IPE to activate$/
  */
  public function waitForIPEtoActivate() {

    $this->getSession()->wait(5000, 'jQuery(".panels-ipe-editing").length > 0');
  }

  /**
  * Wait until the Panels IPE is deactivated.
  *
  * @When /^(?:|I )wait for the Panels IPE to deactivate$/
  */
  public function waitForIPEtoDeactivate() {

    $this->getSession()->wait(5000, 'jQuery(".panels-ipe-editing").length === 0');
  }

  /**
  * Enable the Panels IPE if it's available on the current page.
  *
  * @When /^(?:|I )customize this page with the Panels IPE$/
  */
  public function customizeThisPageIPE() {

    $this->getSession()->getPage()->clickLink('Customize this page');
    $this->waitForIPEtoActivate();
  }

  /**
  * Waiting for suggestion box to appear
  *
  * @Then /^I wait for the suggestion box to appear$/
  */
  public function iWaitForTheSuggestionBoxToAppear() {

    $this->getSession()->wait(5000, "$('#autocomplete').children().length > 0"); 
  }
    
  /**
  * Should see specified minimum records
  *
  * @Given /^I should see at least "([^"]*)" records$/
  */
  public function iShouldSeeAtLeastRecords($count) {

    $element = $this->getSession()->getPage();
    // counts the number of rows in the view table
    $records = $this->getViewDisplayRows($element);
    if ($records == "" || sizeof($records) < $count) {
      throw new Exception("The page (" . $this->getSession()->getCurrentUrl() .
         ") has less than " . $count . " records");
    }
  }

  /**
  * Should see certain column sorted in ascending/descending order
  *
  * @Then /^I should see "([^"]*)" sorted in "([^"]*)" order$/
  */
  public function iShouldSeeSortedInOrder($column, $order) {

    $column_class = "";
    $count = 0;
    $date = FALSE;
    $page = $this->getSession()->getPage();
    $heading = $page->findAll('css', '.view table.views-table th');

    foreach ($heading as $text) {
      if ($text->getText() == $column) {
        $count = 1;
        $class = $text->getAttribute("class");
        $temp = explode(" ", $class);
        $column_class = $temp[1];
        break;
      }
    }

    if ($count == 0) {
      throw new Exception("The page does not have a table with column '" . $column . "'");
    }

    $count = 0;
    $items = $page->findAll('css', '.view table.views-table tr td.'.$column_class);

    // make sure we have the data
    if (sizeof($items)) {
      // put all items in an array
      $loop = 1;
      //date_default_timezone_set ("UTC");
      foreach ($items as $item) {
        $text = $item->getText();

        if ($loop == 1) {
          // check if the text is date field
          if ($this->isStringDate($text)) {
            $date = TRUE;
          }
        }

        if ($date) {
          $orig_arr[] = $this->isStringDate($text);
        }
        else {
          $orig_arr[] = $text;
        }

        $loop = 2;
      }
      // create a temp array for sorting and comparing
      $temp_arr = $orig_arr;
      // sort
      if ($order == "ascending") {
        if ($date) {
          rsort($temp_arr, SORT_NUMERIC);
        }
        else {
          rsort($temp_arr);
        }
      }
      elseif ($order == "descending") {
        if ($date) {
          sort($temp_arr, SORT_NUMERIC);
        }
        else {
          sort($temp_arr);
        }
      }
      // after sorting, compare each index value of temp array & original array
      for ($i = 0; $i < sizeof($temp_arr); $i++) {
        if ($temp_arr[$i] == $orig_arr[$i]) {
          $count++;
        }
      }
      // if all indexs match, then count will be same as array size
      if ($count == sizeof($temp_arr)) {
        return true;
      }
      else {
        throw new Exception("The column '" . $column . "' is not sorted in " . $order . " order");
      }
    }
    else {
      throw new Exception("The column '" . $column . "' is not sorted in " . $order . " order");
    }
  }

//--------------------Bozhidar's FUNCTIONS without step definitions---------------------

  /**
  * Wait for the jQuery AJAX loading to finish. ONLY USE FOR DEBUGGING!
  *
  * @Given /^(?:|I )wait for AJAX loading to finish$/
  */
  public function iWaitForAJAX() {

    $this->getSession()->wait(5000, 'jQuery != undefined && jQuery.active === 0');
  }
      
  /*
  * Function to get the array of records from the current view listing
  * @param $page Object The page object to look into
  * @return $result Array An array of items
  */
  private function getViewDisplayRows($page) {

    $result = "";
    $classes = array(
      'table' => '.view table.views-table tr',
      'grid' => '.view table.views-view-grid tr td',
      'row' => '.view div.views-row'
    );

    foreach ($classes as $type => $class) {
      $result = $page->findAll('css', $class);

      if (!empty($result)) {
        break;
      }
    }

    return $result;
  }

  /*
  * Function to check whether the given string is a date or not
  * @param $string String The string to be checked for
  * @return $return String/Bool - Return timestamp if it is date, false otherwise
  */
  public function isStringDate($string) {

    $return = "";
    $string = trim($string);

    if ($string) {
      $time = strtotime($string);

      if ($time === FALSE) {
        $return = FALSE;
      }
      elseif(is_numeric($time) && strlen($time) == 10) {
        return $time;
      }
      else {
        $return = FALSE;
      }
    }
    else {
      $return = FALSE;
    }

    return $return;
  }
      
  /** 
  * Log in Drupal with provided username and password.
  *
  * @Given /^I log in$/
  */
  public function iLogIn() {

    $session = $this->getSession();
    $page = $session->getPage();
    $usern = $this->params[0];
    $passw = $this->params[1];
    $page->fillField("edit-name", $usern);
    $page->fillField("edit-pass", $passw);
    $page->pressButton("edit-submit");
  }

  /** 
  * Log in Drupal with provided username and password.
  *
  * @Given /^I log in second site$/
  */
  public function iLogInSecondKK() {

    $session = $this->getSession();
    $page = $session->getPage();
    $usern = $this->params[2];
    $passw = $this->params[3];
    $this->getSession()->visit($this->params[5]);
    $page->fillField("edit-name", $usern);
    $page->fillField("edit-pass", $passw);
    $page->pressButton("edit-submit");
  }

  /** 
  * Log in Drupal with provided username and password.
  *
  * @Given /^I log in first site$/
  */
  public function iLogInFirstKK(){

    $session = $this->getSession();
    $page = $session->getPage();
    $usern = $this->params[2];
    $passw = $this->params[3];
    $this->getSession()->visit($this->params[5]);
    $page->fillField("edit-name", $usern);
    $page->fillField("edit-pass", $passw);
    $page->pressButton("edit-submit");
  }
      
 //--------------Georgi's Functions---------------------------//

  public function __call($method, $parameters) {
    // we try to call the method on the Page first
    $page = $this->getSession()->getPage();
    if (method_exists($page, $method)) {
      return call_user_func_array(array($page, $method), $parameters);
    }

    // we try to call the method on the Session
    $session = $this->getSession();
    if (method_exists($session, $method)) {
      return call_user_func_array(array($session, $method), $parameters);
    }

    // could not find the method at all
    throw new \RuntimeException(sprintf(
      'The "%s()" method does not exist.', $method
    ));
  }

  /** 
  * Click on the element with the provided xpath query
  *
  * @When /^(?:|I )click on the element "([^"]*)"$/
  */
  public function iClickOnTheElement($locator) {
    $session=$this->getSession();
    $page=$this->getPage();

    $element = $page->find('css', $locator); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $element->click();
  }

  /** 
  * Reload the current page without GET parameters
  *
  * @When /I reload the current page without GET parameters/
  */
  public function iReloadTheCurrentPageWithoutGetParameters() {

    $hacker = $this->getSession()->getCurrentUrl();
    $hackers = explode ("?destination", $hacker);
    $this->getSession()->visit($hackers[0]);
  }

  /**
  * Fill specified hidden field with certain value
  *
  * @Given /^I fill hidden field "([^"]*)" with "([^"]*)"$/
  */
  public function iFillHiddenFieldWith($input_id, $value) {

    $javascript = "document.getElementById('edit-field-seats-und').value = '5';";
    $this->getSession()->executeScript($javascript);
  }

  /**
  * Set value to element with specified id
  *
  * @Given /^I set value to "([^"]*)"$/
  */
  public function iSelectOption($locator) { 
    
    $this->getSession()->getDriver()->evaluateScript(
      "function(){ 
          var sel = document.getElementById('%s');
          sel.value='15';
      }", $locator);
  }
      
  /** 
  * Click on the div with the provided css locator
  *
  * @When /^(?:|I )click on the div "([^"]*)"$/
  */
  public function iClickOnTheDiv($locator) {

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', 'div.' . $locator); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $element->click();
  }

  /** 
  * Click on the div with the provided title
  *
  * @When /^(?:|I )click on the div with title "([^"]*)"$/
  */
  public function iClickOnTheDivWithTitle($title) {

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', 'div[title="' . $title . '"]'); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $element->click();
  }

  /** 
  * Click on the <a> with the provided css locator
  *
  * @When /^(?:|I )click on the a "([^"]*)"$/
  */
  public function iClickOnTheLink($locator) {

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', 'a.' . $locator); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $element->click();
  }

  /** 
  * Click on the <a> with the provided href
  *     
  * @When /^(?:|I )click on the a with href "([^"]*)"$/
  */
  public function iClickOnTheLinkwithHref($href) {

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', 'a[href="' . $href . '"]'); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $href));
    }

    $element->click();
  }

  /** 
  * Click on the <a> with the provided word that exist in href
  *     
  * @When /^(?:|I )click on the a with href containing "([^"]*)"$/
  */
  public function iClickOnTheLinkwithHrefContaining($word) {

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('xpath', '//a[contains(@href,' . $word . ')]');
   
    //  $element = $session->getPage()->find('css', 'a[href="'..'"]'); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Cannot find link with href containing: "%s"', $word));
    }

    $element->click();
  }

  /** 
  * Click on the <a> with the provided target
  *     
  * @When /^(?:|I )click on the a with target "([^"]*)"$/
  */
  public function iClickOnTheLinkwithTarget($target) {

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', 'a[target="' . $target . '"]'); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $element->click();
  }
 
  /**
  * Use this for time format "HH:MM"
  *
  * @Given /^I click on time in "([^"]*)" region, from dropdown thats is "([^"]*)" hours from now$/
  */
  public function iClickOnTimeWithSpecificFormat($region,$value) {

  	$current_time = date("G", time());
  	$hours_from_now = $current_time + $value;
  	$hours_from_now = (string)$hours_from_now . ':00';
  	$session = $this->getSession();
  	$element = $session->getPage()->find('region', $region)->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', 
                                                                                                            '//*[contains(text(),"' . $hours_from_now . '")]'));
         
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $hours_from_now));
    }

    $element->click();
  }

  public $saved_data;
  public $avg_sum = 0;
  public $count = 0;
  public $tested_url = "";
  public $lines_count = 1;

  /**
  * Save data from provided css locator
  *
  * @When /^I save data from css "([^"]*)"$/
  */
  public function iSaveDataFromCss($locator) {

    global $saved_data;

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $saved_data = $element->getText();
  }

  /**
  * Save page's url
  *
  * @When /^I save url$/
  */
  public function iSaveUrl() {

    global $tested_url;
    $tested_url = $this->getSession()->getCurrentUrl();
  }

  /**
  * Visit saved URL
  *
  * @When /^I go to saved URL$/
  */
  public function iGoToSavedUrl() {

    global $tested_url;
    $this->getSession()->visit($tested_url);
  }

  /**
  * Save data from provided xpath
  *
  * @When /^I save data from xpath "([^"]*)"$/
  */
  public function iSaveDataFromXpath($xpath) {

    global $saved_data;

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath));

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not find xpath emelement: "%s"', $xpath));
    }

    $saved_data = $element->getText();
  }

  /**
  * Assert if the collected information is in range
  *
  * @When /^I check if the collected data is in range "([^"]*)"$/
  */
  public function iAssertTheCollectedDataIsInRange($end) {

    global $saved_data;
    global $tested_url;
    $saved_data = preg_replace("/[^0-9]/", "", $saved_data);
    $temp = $saved_data;
  	$counter = 0;

  	while($temp != 0) {
   		$temp = (int)($temp/10);
   		$counter++;
    }

    if($counter == 7) {
      $saved_data = substr($saved_data, 0, 3);
    } 
    else if($counter == 8){
      $saved_data = substr($saved_data, 0, 4);
    } 
       
    $saved_data = $saved_data/100;
    $saved_data = round($saved_data, 1, PHP_ROUND_HALF_UP);
      
    if($saved_data>$end) {
      $url = $this->getSession()->getCurrentUrl();
      throw new Exception (sprintf('The response time is: "%s" and it is out of the range "%s".<br> The tested element is on URL : "%s".<br> Check the report on URL: "%s"', $saved_data,$end,$tested_url,$url));
    }

    $url = $this->getSession()->getCurrentUrl();
    printf('The response time is: "%s" and it is in the range of "%s".<br> The tested element is on URL : "%s".<br> Check the report on URL : "%s"', 
                                                                                                                      $saved_data, $end, $tested_url, $url);
	
  }

  /**
  * Assert if the collected information average time is in range
  *
  * @When /^I collect data for average test$/
  */
  public function iGetAvgData() {

    global $saved_data;
    global $avg_sum;
    global $count;
    $saved_data = preg_replace("/[^0-9]/", "", $saved_data);
    $temp = $saved_data;
    $counter = 0;

    while($temp != 0) {
      $temp = (int)($temp/10);
      $counter++;
    }

    if($counter == 6) {
      $saved_data = substr($saved_data, 0, 3);
    }
    else if($counter == 7) {
      $saved_data = substr($saved_data, 0, 3);
    }
    else if($counter == 8) {
      $saved_data = substr($saved_data,0,4);
    }
       
    $saved_data = $saved_data/100;
    $saved_data = round( $saved_data, 1, PHP_ROUND_HALF_UP);
    $avg_sum += $saved_data;
    $count++;
  }

  /**
  * Assert if the collected information average time is in range
  *
  * @When /^I assert average data with "([^"]*)"$/
  */
  public function iAssertAvgData($end) {

    global $avg_sum;
    global $count;
    global $tested_url;
    $avg_value = $avg_sum/$count;
    $avg_sum = 0;
    $count = 0;
    $avg_value = round( $avg_value, 1, PHP_ROUND_HALF_UP);
    $old_end = $end;
    $end = $end . ".5";

    if($avg_value>$end) {    
      $url = $this->getSession()->getCurrentUrl();
      
      throw new Exception (sprintf('The average response time is: "%s" and it is out of the range "%s". The tested element is on URL : "%s".<br> Check the report on URL : "%s"', $avg_value, $old_end,$tested_url,$url));
    }

    $url = $this->getSession()->getCurrentUrl();
    printf('The response time is: "%s" and it is in the range of "%s"<br>', $avg_value,$old_end);
    printf('The tested element is on URL: "%s"<br>', $tested_url);
    printf('Check the report on URL : "%s"<br>', $url);
  }     

  /** 
  * Fill field with saved data
  *
  * @Given /^I fill field "([^"]*)" with specified saved data$/
  */
  public function iFillFieldWithSavedDataSpecific($locator) {

    global $saved_data;

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', $locator);

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $temp = preg_replace("/[^0-9]/", "", $saved_data);
    $element->setValue($temp);
  }

  /** 
  * Fill field with saved data
  * 
  * @Given /^I fill field "([^"]*)" with saved data$/
  */
  public function iFillFieldWithSavedData($locator) {

    global $saved_data;

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', $locator);
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $temp = $saved_data;
    $element->setValue($temp);
  }

  /**
  * Selection the first autocomplete option for specified prefix on certain field
  *
  * @When /^I select the first autocomplete option for "([^"]*)" on the "([^"]*)" field$/
  */
  public function iSelectFirstAutocomplete($prefix, $field) {
   
    $session = $this->getSession();
    $page = $session->getPage();
    $element = $page->findField($field);

    if (!$element) {
      throw new ElementNotFoundException($session, NULL, 'named', $field);
    }

    $page->fillField($field, $prefix);
    $xpath = $element->getXpath();
    $driver = $session->getDriver();
   
    $chars = str_split($prefix);
    $last_char = array_pop($chars);
    // autocomplete.js uses key down/up events directly.
    $driver->keyDown($xpath, 8);
    $driver->keyUp($xpath, 8);
    $driver->keyDown($xpath, $last_char);
    $driver->keyUp($xpath, $last_char);
    // Wait for AJAX to finish.
    $this->getSession()->wait(500, '(typeof(jQuery)=="undefined" || (0 === jQuery.active && 0 === jQuery(\':animated\').length))');
    // Press the down arrow to select the first option.
    // And make sure the autocomplete is showing.
    $this->getSession()->wait(5000, 'jQuery("#autocomplete").show().length > 0');
    // And wait for 1 second just to be sure.
    sleep(5);
    $driver->keyDown($xpath, 40);
    $driver->keyUp($xpath, 40);
    // Press the Enter key to confirm selection, copying the value into the field.
    $driver->keyDown($xpath, 13);
    $driver->keyUp($xpath, 13);
    // Wait for AJAX to finish.
    $this->getSession()->wait(500, '(typeof(jQuery) == "undefined" || (0 === jQuery.active && 0 === jQuery(\':animated\').length))');
  }

  /**
  * This gets the window name of the new popup.
  *
  * @hidden
  */
  private function getNewPopup($originalWindowName = NULL) {

    $originalWindowName = $this->getSession()->getDriver()->getWindowName();
    //Get all of the window names first
    $names = $this->getSession()->getWindowNames();
    //Now it should be the last window name
    $last = array_pop($names);

    if (!empty($originalWindowName)) {
      while ($last == $originalWindowName && !empty($names)) {
        $last = array_pop($names);
      }
    }

    return $last;
  }

  /**
  * Switching the perspective to the popup
  *
  * @Then /^I switch to popup$/
  */
  public function iSwitchToPopup() {
    // TODO: Rewrite the expression..
    $originalWindowName = $this->getSession()->getDriver()->getWindowName(); //Get the original name

    if (empty($this->originalWindowName)) {
      $this->originalWindowName = $originalWindowName;
    }

    $popupName = $this->getNewPopup($originalWindowName);

    //Switch to the popup Window
    $this->getSession()->switchToWindow($popupName);
  }

  /**
  * This function check for error messages.
  *
  * @When /^I check for error messages$/
  */
  public function errorCheck() {

    $session = $this->getSession();
    $page = $session->getPage();
    $current_page = $session->getCurrentUrl();
    $errors = $page->findAll('css','.error');

    if(count($errors) > 0) {  
      foreach($errors as $error) {
        $error_message = $error->getText();
        throw new Exception(sprintf("The following error/notice/warning was found on the %s page:\n %s", $current_page,$error_message));
      }
    }
  }

  /**
  * Assert value for certain input
  *
  * @When /^I should see value "([^"]*)" in input "([^"]*)"$/
  */
  public function assertValueInInput($value, $input) {

    $input = "#" . $input;
    $session = $this->getSession();
    $element = $session->getPage()->find('css', $input);
    $text = "";

    if(isset($element)) {
      $text = $element->getValue();
    }
    else {
      throw new Exception(sprintf("Element is null"));
    }
    
    if($text === $value) {
      return true;
    }
    else {
      throw new Exception(sprintf('Value of input : "%s" does not match the text "%s"', $text, $value));
    }
  }

  /**
  * Assert that text exist only once on page
  *
  * @When /^I should see the text "([^"]*)" once$/
  */
  public function assertOnlyOnce($text) {

    $session = $this->getSession();
    $page = $session->getPage();
    $elements = $session->getPage()->findAll('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', '//*[contains(text(),"'. $text .'")]'));

    if(count($elements) > 1) {
      throw new Exception(sprintf("The text is found more than once"));
    }
    else if (count($elements) == 0) {
      throw new Exception(sprintf("The text is missing"));
    }
  }

  /**
  * Maximize window
  *
  * @When /^I maximize window$/
  */
  public function maximizeWindow() {

    $this->getSession()->getDriver()->maximizeWindow();
  }

  public function ValidRegexAndBratMu($url){

    $url = preg_replace("/(\\.)/", '\\\\.', $url);
    // $url = preg_replace("/(\/\/)/", '\\/\\/', $url);
    $url = preg_replace("/(\/)/", '\\/', $url);
    $url = preg_replace("/(\\?)/", '\\\\\?', $url);

    return $url;
  }

  /**
  * Select all checkboxes
  *
  * @When I select all
  */
  public function SelectAll() {

    $session = $this->getSession();
    $page = $session->getPage();
    $checkboxes = $page->findAll('css','input[title="Select all rows in this table"]');
    $checkboxes[1]->click();
  }

  /**
  * Click on the text which is value of input in region
  *
  * @When /^I click on the text "([^"]*)" in input in region "([^"]*)"$/
  */
  public function clickValueInInput($value,$region) {

    $session = $this->getSession();
    $element = $session->getPage()->find('region', $region)->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', 
                                                                                                                        '//input[@value="' . $value . '"]'));

    if(isset($element)) {
      $element->click();
    }
    else {
      throw new Exception(sprintf("Element is null"));
    }
  }

  /* ---------- Dagrofa - GT --------------- */

  /**
  * Assert existence of N circles with certain value
  *
  * @When /^I should not find circles with value "([^"]*)"$/
  */
  public function iAssertNumCirclesWithValue($value) {

    $session = $this->getSession(); 
    $page = $session->getPage();
    $counter = 0;
    $elements = $page->findAll('xpath','//input[@value="' . $value . '"]');

		foreach ($elements as $smth) {
      $counter++;
		}

  	if($counter>0) {
  		throw new Exception (sprintf('There is element that is with value %s', $value));
  	}
  }

// Velizar - cleared main context and moved to kkcontext
    
// ---------------BBD-------------------
  /** Stop the browser session
  * @Then /^I stop the session/
  * @Then /^I end the session/
  */
  public function stopTheSession() {
    $session = $this->getSession();
    $session->stop();
  }

  /**
  * I check certain checkbox's id if it is unchecked
  *
  * @When /^I check "([^"]*)" if not checked yet$/
  */
  public function iCheckIfNotCheckedYet($id) {

    $page = $this->getSession()->getPage();
    $isChecked = $page->findField($id);
    $isChecked = $isChecked->hasAttribute("checked");

    if (!$isChecked) {
      $page->checkField($id);
    }
    else {
      throw new Exception (sprintf('The field %s is already checked.', $id));
    }
  }

  /**
  * I uncheck certain checkbox's id if it's checked
  *
  * @When /^I uncheck "([^"]*)" if checked already$/
  */
  public function iUncheckIfAlreadyChecked($id) {

    $page = $this->getSession()->getPage();
    $isChecked = $page->findField($id);
    $isChecked = $isChecked->hasAttribute("checked");

    if ($isChecked) {
      $page->uncheckField($id);
    }
    else {
      throw new Exception (sprintf('The field %s is already unchecked.', $id));
    }
  }

// --------------BBS--------------------

  /** 
  * Stop when a text is loaded
  * 
  * @Then /^I wait for element with "([^"]*)" selector to appear$/
  */
  public function waitForElementWithSelector($elementClass) {

    $session = $this->getSession();
    $page = $session->getPage();
      
    if($this->getSession()->wait(5000, "jQuery('$elementClass').length > 0")) {
      return true;
    }
    else {
      throw new Exception (sprintf('There is no element with class %s', $elementClass));
    }
  }

  /**
  * Fills in form field with specified id|name|label|value.
  *
  * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with new email "(?P<value>(?:[^"]|\\")*)"$/
  */
  public function fillField($field, $value) {

    $session = $this->getSession();
    $page = $session->getPage();
    $page->fillField("edit-profile-main-mail", $value . "+" . rand(0, 1000000) . "@gmail.com");
  }

  /**
  * Switching to the newly popuped window
  *
  * @Then /^I switch to other window$/
  */
  public function iSwitchToOtherWindow() {

    // TODO: Rewrite the expression..
    $originalWindowName = $this->getSession()->getDriver()->getWindowName(); //Get the original name

    if (empty($this->originalWindowName)) {
        $this->originalWindowName = $originalWindowName;
    }

    $this->getSession()->getPage();
    $popupName = $this->getNewPopup($originalWindowName);
    //Switch to the new Window
    $this->getSession()->switchToWindow($popupName);
  }

  /**
  * Closing the current window
  *
  * @Then /^I close window$/
  */
  public function iCloseWindow() {

    $this->getSession()->stop();
  }

  // --------------Vidko--------------

  /**
  * Selects the multiple dropdown(single select/multiple select) values
  *
  *@param $table
  *    array The list of values to verify
  * @When /^I select the following <fields> with <values>$/
  */
  public function iSelectTheFollowingFieldsWithValues(TableNode $table) {

    $multiple = true;
    $table = $table->getHash();

    foreach ($table as $key => $value) {
      $select = $this->getSession()->getPage()->findField($table[$key]['fields']);
      if(empty($select)) {
        throw new \Exception("The page does not have the " . $table[$key]['fields'] . " field");
      }
      // The default true value for 'multiple' throws an error 'value cannot be an array' for single select fields
      $multiple = $select->getAttribute('multiple') ? true : false;
      $this->getSession()->getPage()->selectFieldOption($table[$key]['fields'], $table[$key]['values'], $multiple);
    }
  }

  public $node_ID;
  /**
  * Extracting node ID from URL. !!!This Function should be used after Save page's url!!!
  * 
  * @When /^I use saved URL to get the node ID$/
  */
  public function nodeIDTrim() {

    global $tested_url;
    global $node_ID;
    $pattern_node_ID = "(\\d+)";
    preg_match($pattern_node_ID, $tested_url, $node_ID_array);
    $node_ID = " (" . $node_ID_array[0] . ")";
  }

  /**
  * Filling the field with string and node ID
  * To use this function you need NodeID which can be get with NodeIDTrim function  
  *
  * @When /^I fill "([^"]*)" field with string "([^"]*)" and the saved Node ID$/
  */
  public function fillFieldWithStringAndNodeID($field, $value) {

    global $node_ID;

    $session = $this->getSession();
    $element = $session->getPage()->find('css', $field);
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $field));
    }

    $temp = $value . $node_ID;
    $element->setValue($temp);
  }

  /**
  * Attempts to find a link in a table row containing giving text.
  * Go to the administer content types screen, find the row, click on edit link, then get the node ID and return to the admin content types screen.
  *
  * @Given I go to node with title :rowText and save the node ID
  */
  public function goToNodeWithTitleAndGetTheNodeID($rowText) {

    $this->getSession()->visit($this->locatePath('/admin/content'));
    sleep(1);
    $session = $this->getSession();
    $page = $session->getPage();
    $page->fillField("edit-title", $rowText);
    sleep(1);
    $this->getSession()->getPage()->pressButton('edit-submit-admin-views-node');
    sleep(1);
    $link = "edit";
    $page = $this->getSession()->getPage();

    if ($link = $this->getTableRow($page, $rowText)->findLink($link)) {
      $link->click();
    }

    sleep(1);
    global $tested_url;
    global $node_ID;
    $tested_url = $this->getSession()->getCurrentUrl();
    $pattern_node_ID = "(\\d+)";
    preg_match($pattern_node_ID, $tested_url, $node_ID_array);
    $node_ID = " (" . $node_ID_array[0] . ")";
    sleep(1);
    $this->getSession()->visit($this->locatePath('/admin/content'));
    sleep(1);
    $this->getSession()->getPage()->pressButton('edit-reset');
  }

  /**
  * Function for searching specified node by title in admin/content
  *
  * @Given I filter the content listing page to see only nodes with :rowText title
  */
  public function contentTypesAndFilterByTitle($rowText) {

    $this->getSession()->visit($this->locatePath('/admin/content'));
    sleep(1);
    $session = $this->getSession();
    $page = $session->getPage();
    $page->fillField("edit-title", $rowText);
    sleep(1);
    $this->getSession()->getPage()->pressButton('edit-submit-admin-views-node');
    sleep(5);
  }

  /**
  * Function for clearing the cache and returning to the front page
  *
  * @Given /^I clear cache$/
  */
  public function clearCache() {

    $this->getSession()->visit($this->locatePath('/admin/config/development/performance'));
    sleep(1);
    $this->getSession()->getPage()->pressButton('edit-clear');
    sleep(1);
    $this->getSession()->visit($this->locatePath('/'));
  }

  /**
  * The iframe in certain element has specified id
  *
  * @Then the iframe in element :arg1 has id :arg2
  */
  public function theIframeInElementHasId($element_id, $iframe_id) {

    $function = <<<JS
    (function(){
      var elem = document.getElementById("$element_id");
      console.log(elem);
      var iframes = elem.getElementsByTagName('iframe');
      console.log(iframes);
      var f = iframes[0];
      f.id = "$iframe_id";
    })()
JS;

    try {
      $this->getSession()->executeScript($function);
    }
    catch(Exception $e) {
      throw new \Exception(sprintf('No iframe found in the element "%s" on the page "%s".', $element_id, $this->getSession()->getCurrentUrl()));
    }
  }

  /** 
  * Get text from element
  *
  * @Given /^I write "([^"]+)" into "([^"]+)" wysiwyg$/
  */
  public function writeElementText($text,$iframeId) {

    $this->getSession()->wait(500);
    $id = 'behat' . round(microtime(true) * 1000);
    $this->theIframeInElementHasId($iframeId, $id);
    $this->iSwithToIframe($id);
    $text = json_encode($text);

    $function = <<<JS
    (function(){
      var elem = document.getElementsByTagName("*");
      g=elem[0];
      g.innerHTML=$text;
    })()
JS;

    $this->getSession()->executeScript($function);
    $this->iSwitchBackToOriginalWindow();
  }

  /**
  * Fill nasty chosen fields with javascript
  *
  * @When I select :option from chosen :selector
  */
  public function lselectOptionWithJavascript($selector, $option) {

    $page = $this->getSession()->getPage();
    // Search field
    $field = $page->findField($selector);
    if (!isset($field)) {
      throw new \InvalidArgumentException(sprintf('Cannot find select: "%s"', $selector));
    }
    // Search for option
    $opt = $field->find('named', array(
      'option', $this->getSession()->getSelectorsHandler()->xpathLiteral($option)
    ));
    if (!isset($opt)) {
     throw new \InvalidArgumentException(sprintf('Cannot find option: "%s"', $option));
    }

    $value = $field->getValue();
    $newValue = $opt->getAttribute('value');
    if(is_array($value)) {
      if(!in_array($newValue, $value)){
        $value[] = $newValue;
      } 
    } 
    else {
      $value = $newValue;
    }

    $valueEncoded = json_encode($value);

    $fieldID = $field->getAttribute('ID');
    $script = <<<EOS
      (function($) {
        
        $("#$fieldID")
          .val($valueEncoded)
          .change()
          .trigger('liszt:updated')
          .trigger('chosen:updated');
      })(jQuery);
EOS;

    $this->getSession()->getDriver()->executeScript($script);
  }

  /**
  * Should see image title in specified region
  *
  * @Then /^I should see the image title "(?P<link>[^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
  */
  public function assertTitleRegion($title, $region) {
    $regionObj = $this->getRegion($region);
    $element = $regionObj->find('css', 'img');
    $tmp = $element->getAttribute('title');

    if ($title == $tmp) {
      $result = $title;
    }

    if (empty($result)) {
      throw new \Exception(sprintf('No title text matching "%s" in the "%s" region on the page %s', $title, $tmp, $this->getSession()->getCurrentUrl()));
    }
  }

  /**
  * Checking for specified text for certain seconds with interval of 1 second
  *
  * @Then I should check for the text :arg1 for :arg2 seconds
  */
  public function checkIfPageContainsTextForTime($text, $sec) {

    $flag = FALSE;
    $session = $this->getSession();

    for ($i = 0; $i < $sec; $i++) {
        $element = $session->getPage()->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', '//*[contains(text(),"' . $text . '")]'));
        if(!isset($element)) {
          sleep(1);
        }
        else {
          $flag = TRUE;
        }

    }

    if($flag == FALSE) {
      throw new Exception(sprintf("Can not find the text after '%s' seconds", $sec));
    }
  }

  /**
  * Showing the page
  *
  * @Then /^show me the page$/
  */
  public function show_me_the_page() {

    $name = "behat-" . date('Y-m-d H:i:s') . ".html";
    $html = $this->getSession()->getDriver()->getContent();
    file_put_contents($name, $html);
  }
}

