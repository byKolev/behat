<?php
	/**
     * Moves user to the specified path.
     *
     * @Given /^I am in the "([^"]*)" path$/
     *
     * @param   string $path
     */
    public function iAmInThePath($path)
    {
        $this->moveToNewPath($path);
    }

    /**
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
   * @Then /^I switch to popup$/
   */
  public function iSwitchToPopup() {
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
   * @Then /^I switch back to original window$/
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
   * @Then /^I should see the text "(?P<text>[^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
   * @Then /^I should see the "(?P<text>[^"]*)" text in the "(?P<region>[^"]*)"(?:| region)$/
   *
   * @throws \Exception
   *   If region or header within it cannot be found.
   */
  public function assertRegionText($text, $region) 
  {
    $regionObj = $this->getRegion($region);
    foreach (array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'div', 'span') as $tag) 
    {
      $elements = $regionObj->findAll('css', $tag);
      if (!empty($elements)) 
      {
          foreach ($elements as $element) 
          {
            $tempArray = explode(" ",trim($element->getText()));
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

  /**
   * Find that text doesn not belong to a specific region.
   *
   * @Then /^I should not see the text "(?P<text>[^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
   * @Then /^I should not see the "(?P<text>[^"]*)" text in the "(?P<region>[^"]*)"(?:| region)$/
   *
   * @throws \Exception
   *   If region or header within it cannot be found.
   */
  public function NegativeAssertRegionText($text, $region) {
    $regionObj = $this->getRegion($region);
    foreach (array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'div', 'span') as $tag) {
      $elements = $regionObj->findAll('css', $tag);
      if (!empty($elements)) 
      {
        foreach ($elements as $element) 
          {
            $tempArray = explode(" ",trim($element->getText()));
            foreach($tempArray as $value) 
            {
              if($value == $text)
              {
                throw new Exception("There is such text $text in that region $region.");
              }
            }
          }
      }
    }
    return;
  }

  /**
     * Click on text in specific region
     *
     * @When /^I click on the text "([^"]*)" in the "(?P<region>[^"]*)"(?:| region)$/
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


    /**
       * @Given /^I delete the "([^"]*)" user$/
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

      /**
          * @Given /^I create user with username "([^"]*)" role "([^"]*)" password "([^"]*)" and email "([^"]*)"$/
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

    /**
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
     * @When /^(?:|I )wait for the Panels IPE to activate$/
     *
     * Wait until the Panels IPE is activated.
     */
    public function waitForIPEtoActivate() {
      $this->getSession()->wait(5000, 'jQuery(".panels-ipe-editing").length > 0');
    }

    /**
     * @When /^(?:|I )wait for the Panels IPE to deactivate$/
     *
     * Wait until the Panels IPE is deactivated.
     */
    public function waitForIPEtoDeactivate() {
      $this->getSession()->wait(5000, 'jQuery(".panels-ipe-editing").length === 0');
    }

    /**
     * @When /^(?:|I )customize this page with the Panels IPE$/
     *
     * Enable the Panels IPE if it's available on the current page.
     */
    public function customizeThisPageIPE() {
      $this->getSession()->getPage()->clickLink('Customize this page');
      $this->waitForIPEtoActivate();
    }

    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getMainContext()->getSession()->wait(5000,
            "$('.suggestions-results').children().length > 0"
        );
    }
    
    /**
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
     * @Then /^I should see "([^"]*)" sorted in "([^"]*)" order$/
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
//--------------------Bozhidar's FUNCTIONS without step definitions---------------------

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
?>