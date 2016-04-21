<?php
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Behat\Context\SnippetAcceptingContext;


/**
 * Features context.
 * @author Bozhidar Boshnakov <bboshnakov91@gmail.com> and awesome QA Team (Toni Kolev, George Tarkalanov, Velizar Zlatev, Daniel Angelov, Stanislav Todorov)
 */
class FeatureContext extends Drupal\DrupalExtension\Context\DrupalContext implements SnippetAcceptingContext
{

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


    public function __construct(array $parameters)
    {
        $this->params = $parameters;
    }

     /** Push a Submit, Delete, Run etc. button.
      *
      * @Given /^I push the "([^"]+)" button$/
      *
      */
    public function iPushTheButton($button)
    {
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

      if (!isset($buttons[$button])) 
        {
          throw new InvalidArgumentException(sprintf('"%s" button is not mapped. Map the button in your function.', $button));
        }
      $this->getSession()->getPage()->pressButton($buttons[$button]);
    }
    
    
    /**
     * Click some text
     *
     * @When /^I click on the text "([^"]*)"$/
     *
     */
    public function iClickOnTheText($text)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('xpath', '//*[contains(text(),"'. $text .'")]'));
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
        }
        $element->click();
    }

    /** Confirms the currently opened popup.
     *
     * @When /^(?:|I )confirm the popup$/
     *
     */
    public function confirmPopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }

   /** Cancles the currently opened popup.
    *
    * @When /^(?:|I )cancel the popup$/
    *
    */
    public function cancelPopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
    }

    /** Click on the element with the provided xpath query.
     *
     * @When /^I click on the element with xpath "([^"]*)"$/
     *
     */
    public function iClickOnTheElementWithXPath($xpath)
    {
      $session = $this->getSession(); // get the mink session
      $element = $session->getPage()->find(
          'xpath',
          $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
      ); // runs the actual query and returns the element
      // errors must not pass silently
      if ($element === null) {
          throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s" for xpath: "%g"', $element, $xpath));
      }
      // ok, let's click on it
      $element->click();
    }

    /** Set value to the element with the provided xpath query.
     *
     * @When /^I set value "([^"]*)" to the element with xpath "([^"]*)"$/
     *
     */
    public function iSetValueToTheElementWithXPath($value,$xpath)
    {
      $session = $this->getSession(); // get the mink session
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
     *
     */
    public function iClickOnTheElementWithCSSSelector($cssSelector)
    {
      $session = $this->getSession();
      $element = $session->getPage()->find(
          'css',
          $session->getSelectorsHandler()->selectorToXpath('css', $cssSelector) // just changed xpath to css
      );
      if (null === $element) {
          throw new \InvalidArgumentException(sprintf('Could not evaluate CSS Selector: "%s"', $cssSelector));
      }

      $element->click();
    }

    /** The browser sleeps for seconds.
     *
     * @Given /^I sleep for "([^"]+)"(?: seconds)?$/
     *
     */
    public function iSleepForSeconds($var)
    {
      $seconds = ((int)$var);
      sleep($seconds);
    }

    /** The browser waits for seconds.
     *
     * @Given /^I wait for "([^"]+)"(?: seconds)?$/
     *
     */
    public function iWaitForSeconds($var)
    {
      $seconds = ((int)$var) * 1000;
      $this->getSession()->wait($seconds);
    }

    /**
     *
     * @Given /^I (?:am on the|go to the) "([^"]+)"(?: page)?$/
     *
     */
    public function iAmOnThe($page)
    {
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
      if (!isset($pages[$page])) 
        {
          throw new InvalidArgumentException(sprintf('"%s" page is not mapped. Map the page in your function.', $page));
        }
      $this->getSession()->visit($this->locatePath($pages[$page]));
    }

    /** Log in Drupal with provided username and password.
     *
     * @Given /^I log in as "([^"]+)" "([^"]+)"$/
     *
     */
    public function iLogInAs($username, $password)
    {
      $session = $this->getSession();
      $page = $session->getPage();
      $page->fillField("edit-name", $username);
      $page->fillField("edit-pass", $password);
      $page->pressButton("edit-submit");
    }

    /**
     *
     * @Then /^(?:|I )put a breakpoint$/
     *
     */
    public function breakpoint() {
      fwrite(STDOUT, "\033[s \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
      while (fgets(STDIN, 1024) == '') {}
      fwrite(STDOUT, "\033[u");
      return;
    }

    /** Visit profile's homepage with either HTTP or HTTPS
     *
     * @Given /^I go to home page using "([^"]*)" protocol$/
     *
     */
    public function iGoHomePageThrough($protocol)
    {
      $base_url = $this->getMinkParameter('base_url');
      if ($protocol == 'HTTP') {
          if ($base_url[4] == ':') $this->getSession()->visit($base_url);
          else {
              $string = $base_url;
              $pattern = '/https/';
              $replacement = 'http';
              $urlToVisit = preg_replace($pattern, $replacement, $string);
              $this->getSession()->visit($urlToVisit);
          }
      }

      else if ($protocol == 'HTTPS') {
          if ($base_url[4] == 's') $this->getSession()->visit($base_url);
          else {
              $string2 = $base_url;
              $pattern2 = '/http/';
              $replacement2 = 'https';
              $urlToVisit2 = preg_replace($pattern2, $replacement2, $string2);
              $this->getSession()->visit($urlToVisit2);
          }
      }
      else throw new Exception('You are supposed to select HTTP or HTTPS as a protocol.');
    }

    /**Scroll element with specific id to the top
     *
     * @When I scroll element with id :elementId to the top
     *
     */
    public function scrollIntoView($elementId) {
        $function = <<<JS
        (function(){
        var elem = document.getElementById("$elementId");
        elem.scrollIntoView(false);
        })()
JS;
        try {
            $this->getSession()->executeScript($function);
        }
        catch(Exception $e) {
            throw new \Exception("Probably I was not able to find an element with this id...actually I don't know what is the problem :( ");
        }
    }

    /** Scroll element with specific class name to the top
     *
     * @When I scroll element with class :elementClass to the top
     *
     */
    public function scrollIntoView2($elementClass) {
        $function = <<<JS
        (function(){
          var elem = document.getElementsByClassName("$elementClass");
          elem = elem[0];
          elem.scrollIntoView(false);
        })()
JS;
        try {
            $this->getSession()->executeScript($function);
        }
        catch(Exception $e) {
            throw new \Exception("Probably I was not able to find an element with this id...actually I don't know what is the problem :( ");
        }
    }

    /** Wait until page load, with optional callback parameter
     *
     * @Given /^I wait until the page loads "([^"]*)"$/
     *
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

  /** Switching to the select iframe by css selector
   *
   * @Given /^I switch to iframe "([^"]*)"$/
   *
   */
  public function iSwithToIframe($arg1) {
   $originalWindowName = $this->getSession()->getDriver()->getWindowName(); //Get the original name

      if (empty($this->originalWindowName)) {
          $this->originalWindowName = $originalWindowName;
      }
    $this->getSession()->switchToIframe($arg1);
  }

  /** Switch back to original windows from iframe or popup
   *
   * @Then /^I switch back to original window$/
   *
   */
  public function iSwitchBackToOriginalWindow() {
      //Switch to the original window
      $this->getSession()->switchToWindow($this->originalWindowName);
      $this->getSession()->wait(5000); 
  }

  /**
   * Find that heading is not in a specific region.
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

  /**
   * Find that text belongs to a specific region.
   *
   * @Then /^I should see the text "([^"]*)" in the "(?P<region>[^"]*)"(?:| region)l$/
   * @Then /^I should see the "([^"]*)" text in the "(?P<region>[^"]*)"(?:| region)$/
   *
   * @throws \Exception
   *   If region or header within it cannot be found.
   */
  
  public function assertRegionText($text, $region) 
  {
    $regionObj = $this->getRegion($region);
    foreach (array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'div', 'span', 'p') as $tag) 
    {
      $elements = $regionObj->findAll('css', $tag);
      if (!empty($elements)) 
      {
          foreach ($elements as $element) 
          {
            $tempArray = explode(" ",$element->getText());
            foreach($tempArray as $value) 
            {
              if($value == $text)
              {
                return;
              }
            }
          }
      }
    }
    throw new Exception("There is no such text $text in that region $region.");
  }
  
  /** Click on text in specific region
   *
   * @When /^I click on the text "([^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
   *
   */
    public function iClickOnTheTextInRegion($text, $region)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find('region', $region)->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', '//*[contains(text(),"'. $text .'")]'));
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
        }
        $element->click();
 
    }

  /** Return a region from the current page.
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

  /** Delete user trough drush
   *
   * @Given /^I delete the "([^"]*)" user$/
   *
   */
    public function iDeleteTheUser($userToDelete) 
    {
        $options = array(
          'yes' => NULL,
          'delete-content' => NULL,
        ); 
        try 
        {
          $this->getDriver()->drush('user-cancel', array($userToDelete),$options);
        }
        catch (RuntimeException $e)
        {
          throw new Exception("User with an username is not existing or the drush command user-cancel cannot be executed.");
        }
        $this->getDriver()->processBatch();
    }

    /** Creates user with specific username password and role
     *
     * @Given /^I create user with username "([^"]*)" role "([^"]*)" password "([^"]*)" and email "([^"]*)"$/
     *
     */
      public function iCreateUserWithUsernameRolePasswordAndEmail($user, $role, $password, $email)
      {
        $options = array(
          'password' => $password,
          'mail' => $email,
        );
        $arguments = array(
          sprintf('"%s"', $role),
          $user,
        );
        try 
        {
          $this->getDriver()->drush('user-create', array($user), $options);
          $this->getDriver()->drush('user-add-role', $arguments);
        }
        catch (RuntimeException $e)
        {
          throw new Exception("The username $user or the email $email are already taken or the drush command user-create cannot be executed.");
        }
    }

    /** Assert existing tag and text in region
     *
     * @Then /^I should see "([^"]*)" in the "([^"]*)" element in the "([^"]*)" region$/
     *
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

    /** Assert not existing tag and text in region
     *
     * @Then /^I should not see "([^"]*)" in the "([^"]*)" element in the "([^"]*)" region$/
     *
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

    /** Assert existing image with alter in specific region
     *
     * @Then /^I should see the image alt "(?P<link>[^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
     *
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

    /** Wait until the Panels IPE is activated.
     * 
     * @When /^(?:|I )wait for the Panels IPE to activate$/
     * 
     */
    public function waitForIPEtoActivate() {
      $this->getSession()->wait(5000, 'jQuery(".panels-ipe-editing").length > 0');
    }

    /** Wait until the Panels IPE is deactivated.
     *
     * @When /^(?:|I )wait for the Panels IPE to deactivate$/
     * 
     */
    public function waitForIPEtoDeactivate() {
      $this->getSession()->wait(5000, 'jQuery(".panels-ipe-editing").length === 0');
    }

    /** Enable the Panels IPE if it's available on the current page.
     * 
     * @When /^(?:|I )customize this page with the Panels IPE$/
     * 
     */
    public function customizeThisPageIPE() {
      $this->getSession()->getPage()->clickLink('Customize this page');
      $this->waitForIPEtoActivate();
    }

    /** Wait for suggestion box to appear
     *
     * @Then /^I wait for the suggestion box to appear$/
     *
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(5000, "$('#autocomplete').children().length > 0"); 
    }
    
    /** Assert that atleast number of records are appearing
     *
     * @Given /^I should see at least "([^"]*)" records$/
     *
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
     *
     * @Then /^I should see "([^"]*)" sorted in "([^"]*)" order$/
     *
     */
    public function iShouldSeeSortedInOrder($column, $order)
    {
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


    /**
     * @Given /^(?:|I )wait for AJAX loading to finish$/
     *
     * Wait for the jQuery AJAX loading to finish. ONLY USE FOR DEBUGGING!
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
        $return = "";;
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

    /** Log in Drupal with provided username and password as parameters from YML file.
     *
     * @Given /^I log in$/
     *
     */
    public function iLogIn()
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $usern = $this->params[0];
        $passw = $this->params[1];
        $page->fillField("edit-name", $usern);
        $page->fillField("edit-pass", $passw);
        $page->pressButton("edit-submit");
    }
      
 //--------------Georgi's Functions---------------------------//


  public function __call($method, $parameters)
 {
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

    /** Click on the element with the provided CSS selector
     *
     * @When /^(?:|I )click on the element "([^"]*)"$/
     */
    public function iClickOnTheElement($locator)
    {
      $element = $this->find('css', $locator); // runs the actual query and returns the element
      if (null === $element) {
        throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
      }
      $element->click();
    }

    /** Fill hidden field with data
     *
     * @Given /^I fill hidden field "([^"]*)" with "([^"]*)"$/
     *
     */
    public function iFillHiddenFieldWith($input_id, $value)
    {

        $javascript = "document.getElementById('edit-field-seats-und').value = '5';";
        $this->getSession()->executeScript($javascript);
    }

    /** Click on the div with the provided css locator
     *
     * @When /^(?:|I )click on the div "([^"]*)"$/
     *
     */
    public function iClickOnTheDiv($locator)
    {
        $session = $this->getSession(); // get the mink session
        $element = $session->getPage()->find('css', 'div.'.$locator); // runs the actual query and returns the element

      // errors must not pass silently
       if (null === $element) {
          throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
       }

        // ok, let's click on it
        $element->click();

    }

    /** Click on the div with the provided title
     *
     * @When /^(?:|I )click on the div with title "([^"]*)"$/
     *
     */
    public function iClickOnTheDivWithTitle($title)
    {
      $session = $this->getSession(); // get the mink session
      $element = $session->getPage()->find('css', 'div[title="'.$title.'"]'); // runs the actual query and returns the element

     if (null === $element) {
        throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
     }

      $element->click();
    }

    /** Click on the <a> with the provided css locator
     *
     * @When /^(?:|I )click on the a "([^"]*)"$/
     */
    public function iClickOnTheLink($locator)
    {
      $session = $this->getSession(); // get the mink session
      $element = $session->getPage()->find('css', 'a.'.$locator); // runs the actual query and returns the element

      if (null === $element) {
        throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
      }

      $element->click();
    }

    /** Click on link with the provided href
     *     
     * @When /^(?:|I )click on the a with href "([^"]*)"$/
     *
     */
    public function iClickOnTheLinkwithHref($href)
    {
      $session = $this->getSession(); // get the mink session
      $element = $session->getPage()->find('css', 'a[href="'.$href.'"]'); // runs the actual query and returns the element
     if (null === $element) {
        throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $href));
     }
      $element->click();
    }

    /** Click on link with the provided word that exist in href
     *     
     * @When /^(?:|I )click on the a with href containing "([^"]*)"$/
     *
     */
    public function iClickOnTheLinkwithHrefContaining($word)
    {
      $session = $this->getSession(); // get the mink session
      $element= $session->getPage()->find('xpath','//a[contains(@href,'.$word.')]');
      if (null === $element) {
        throw new \InvalidArgumentException(sprintf('Cannot find link with href containing: "%s"', $word));
      }
      $element->click();
    }


    /** Click on the <a> with the provided target
     *     
     * @When /^(?:|I )click on the a with target "([^"]*)"$/
     */
    public function iClickOnTheLinkwithTarget($target)
    {
      $session = $this->getSession(); // get the mink session
      $element = $session->getPage()->find('css', 'a[target="'.$target.'"]'); // runs the actual query and returns the element
     if (null === $element) {
        throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
     }
      $element->click();
    }

public $saved_data;

    /**
     * Save data from provided css locator
     *
     * @When /^I save data from "([^"]*)"$/
     *
     */
    public function iSaveDataFromCss($locator)
    {
      global $saved_data;

      $session = $this->getSession(); 
      $element = $session->getPage()->find('css',$locator); 
      if (null === $element) {
          throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
      }
      $saved_data=$element->getText();
    }

    /** Save page's url
     *
     * @When /^I save url$/
     *
     */
    public function iSaveUrl()
    {
        global $tested_url;
        $tested_url=$this->getSession()->getCurrentUrl();
    }


    /**
     * Save data from provided xpath
     *
     * @When /^I save data from xpath "([^"]*)"$/
     */
    public function iSaveDataFromXpath($xpath)
    {
      global $saved_data;

      $session = $this->getSession(); // get the mink session
      $element = $session->getPage()->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath));
      if (null === $element) {
          throw new \InvalidArgumentException(sprintf('Could not find xpath emelement: "%s"', $xpath));
      }
      $saved_data=$element->getText();
    }


    /** Fill field with saved data
     *
     * @Given /^I fill field "([^"]*)" with saved data$/
     *
     */
    public function iFillFieldWithSavedData($locator)
    {
      global $saved_data;

      $session = $this->getSession(); // get the mink session
      $element = $session->getPage()->find('css', $locator);
      if (null === $element) {
              throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
          }
      $temp=$saved_data;
      $element->setValue($temp);
    }


    /** Selects first autocomplete option for word on field
     *
     * @When /^I select the first autocomplete option for "([^"]*)" on the "([^"]*)" field$/
     *
     */
      public function iSelectFirstAutocomplete($prefix, $field) 
      {
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
        $this->getSession()->wait(500, '(typeof(jQuery)=="undefined" || (0 === jQuery.active && 0 === jQuery(\':animated\').length))');
    }

    /**
    * This function check for error messages.
    * @When /^I check for error messages$/
    */
    public function errorCheck()
    {
      $session=$this->getSession();
      $page=$session->getPage();
      $element=$page->findAll('css','.messages error');
      if(isset($element))
      {
        $function=<<<JS
        var elem=document.getElementById("console");
        var lis= elem.getElementsByTagName("li");
        var f;
        var i;
        for(i=0;i<lis.length;i++)
        {
        f=lis[i];
        f.id="error_msg";
        }
JS;
        $this->getSession()->executeScript($function);
        $errors=$this->getSession()->getPage()->findAll("css","#error_msg");
       
        foreach ($errors as $error) {
         $error_text=$error->getText();
        }
        
      }


  }
     /**
     * Assert Value for certain Input
     *
     * @When /^I should see value "([^"]*)" in input "([^"]*)"$/
     */
    public function assertValueInInput($value,$input)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find('css',$input);
        $text="";
        if(isset($element)) $text=$element->getValue();
        else throw new Exception(sprintf("Element is null"));
        
        if($text===$value) return true;
        else throw new Exception(sprintf('Value of input : "%s" does not match the text "%s"', $text,$value));
    }

    /** Stop the browser session
     *
     * @Then /^I stop the session/
     *
     */
    public function stopTheSession()
      {
          $session = $this->getSession();
          $session->stop();
      }

    /** Checks a checkbox if it wasn't checked yet
     *
     * @When /^I check "([^"]*)" if not checked yet$/
     *
     */
    public function iCheckIfNotCheckedYet($id)
      {
          $Page = $this->getSession()->getPage();
          $isChecked = $Page->find('css', 'input[type="checkbox"]:checked#' . $id);
          if (!$isChecked) 
          {
              $this->getSession()->getPage()->checkField($id);
          }
     }


    /** Wait for element to appear
     *
     * @Then /^I wait for element with "([^"]*)" selector to appear$/
     *
     */
    public function waitForElementWithSelector($elementClass)
    {
          $session=$this->getSession();
          $page=$session->getPage();
          
         if($this->getSession()->wait(5000, "jQuery('$elementClass').length > 0"))
          return true;
         else
         throw new Exception (sprintf('There is no element with class %s', $elementClass));
    }

    /** Switch to another open window
     * @Then /^I switch to other window$/
     */
    public function iSwitchToOtherWindow() {
        $originalWindowName = $this->getSession()->getDriver()->getWindowName(); //Get the original name
        if (empty($this->originalWindowName)) {
            $this->originalWindowName = $originalWindowName;
        }
        $this->getSession()->getPage();
        $popupName = $this->getNewPopup($originalWindowName);
        $this->getSession()->switchToWindow($popupName);
    }

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

    /**
    * Function for clearing the cache and returning to the front page
    *
    * @Given /^I clear cache$/
    */
    public function ClearCache() {
      $this->getSession()->visit($this->locatePath('/admin/config/development/performance'));
      sleep(1);
      $this->getSession()->getPage()->pressButton('edit-clear');
      sleep(1);
      $this->getSession()->visit($this->locatePath('/'));
    }


     /** Add an id to iframe in element
     *
     * @Then the iframe in element :arg1 has id :arg2
     *
     */
    public function theIframeInElementHasId($element_id, $iframe_id)
    {
       $function = <<<JS
    (function(){
    var elem = document.getElementById("$element_id");
    var iframes = elem.getElementsByTagName('iframe');
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


   /** Fill in text in wysiwyg
    *
    * @Given /^write "([^"]+)" into wysiwyg$/
    *
    */
    public function writeElementText($text)
    {
      $this->theIframeInElementHasId("cke_edit-body-und-0-value", "behat_id");
      $this->iSwithToIframe("behat_id");
      $text = json_encode($text);
      $function = <<<JS
      (function(){
      var elem = document.getElementsByTagName('p');
      g=elem[0];
      g.innerHTML=$text;
      })()
JS;
      $this->getSession()->executeScript($function);
      $this->iSwitchBackToOriginalWindow();
    }

  /** Fill nasty chosen fields with javascript 
   * 
   * @When I select :option from chosen :selector
   *
   */
  public function selectOptionWithJavascript($selector, $option) {
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
      if(!in_array($newValue, $value)) $value[] = $newValue;
    } else {
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
 

    /** Count appearance of item with css selector
     *
     * @Given /^Count appearance of "([^"]*)"$/
     *
     */
    public function CountAppOf($locator)
    {
       $session=$this->getSession();
       $page=$session->getPage();
       $element=$page->findAll('css',$locator);
      return count($element);

    }

    /** Click on saved data.
     * @Given /^I click on the saved data$/
     */
    public function iClickOnSavedData()
    {
      global $saved_data;

      $session = $this->getSession(); // get the mink session
      $element = $session->getPage()->find('xpath',$session->getSelectorsHandler()->selectorToXpath('xpath', '//*[contains(text(),"'.$saved_data.'")]'));
      if (null === $element) {
              throw new \InvalidArgumentException(sprintf('Could not find "%s" on the page ', $saved_data));
          }
      $element->click();
    }

    /** Should see saved data.
     * @Given /^I should see saved data$/
     */
    public function iShouldSeeSavedData()
    {
      global $saved_data;

      $text = $this->getSession()->getPage()->getText(); 
      if (strpos($text,$saved_data)) return true;
      else 
      {
        throw new Exception (sprintf('The saved data "%s" does not appear on the page',$saved_data));
      } 
    }

   /**
    * Click on input with certain value
    *
    * @When /^I click on the input with value "([^"]*)"$/
    *
    */
    public function clickOnInputWithValue($value)
    {
      $flag=0;
         $function = <<<JS
    (function(){
    var inputs = document.getElementsByTagName("input");
    var f;l
    var i;
    for(i=0;i<inputs.length;i++)
    {
     f = inputs[i];
     f.classList.add("input_special");
    }
    })()
JS;
    
    $this->getSession()->executeScript($function);
    $session = $this->getSession();
        $page = $session->getPage();
        $elements=$page->findAll("css",".input_special");
        foreach ($elements as $element) {
          if($element->getValue()==$value)
          {
            $element->click(); $flag=1;
          }
        }
        if($flag==0) throw new Exception(sprintf('There is no input with this value "%s"',$value));
    }

}

