<?php
use Behat\Behat\Tester\Exception\PendingException;

class SyngentaContext extends FeatureContext
{

    public function __construct(array $parameters)
    {
        $this->params = $parameters;
    }

    /** Log in to the main site with the user & password from the YAML.
     *
     * @Given /^I log in to main site$/
     */
    public function iLogInToSyngentaMainSite()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $usern = $this->params['mainSite']['user'];
        $passw = $this->params['mainSite']['pass'];
        $page->fillField("edit-name", $usern);
        $page->fillField("edit-pass", $passw);
        $page->pressButton("edit-submit");
    }

    /** Log in Drupal with provided username and password.
     * @Given /^I log in to DAS$/
     */
    public function iLogInToDAS()
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $usern = $this->params['DAS']['user'];
        $passw = $this->params['DAS']['pass'];
        $page->fillField("edit-name", $usern);
        $page->fillField("edit-pass", $passw);
        $page->pressButton("edit-submit");
    }

    /** Log in Drupal with provided username and password.
     * @Given /^I log in to second country site$/
     */
    public function iLogInToSecondCountrySite()
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $usern = $this->params['secondCountrySite']['user'];
        $passw = $this->params['secondCountrySite']['pass'];
        $page->fillField("edit-name", $usern);
        $page->fillField("edit-pass", $passw);
        $page->pressButton("edit-submit");
    }

    /** Log in to Facebook using the provided credentials in the YAML
     *
     * @Given /^I log in to Facebook$/
     */
    public function iLogInToFacebook()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $usern = $this->params['Facebook']['user'];
        $passw = $this->params['Facebook']['pass'];
        $page->fillField("email", $usern);
        $page->fillField("pass", $passw);
        #$page->clickLink("Log In");
        $page->pressButton("loginbutton");
    }

    /** Go to specific Syngenta DAS page using parameter
     *
     * @Given /^I go to DAS "([^"]*)"$/
     */
    public function iGoToDAS($das)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params['DAS']['url'].$das;
        $this->getSession()->visit($this->locatePath($urlPath));
    }

    /** Initially go to second country site page using parameter
     *
     * @Given /^I initially go to second country site "([^"]*)"$/
     */
    public function iInitiallyGoToSecondCountrySite($SCS)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params['secondCountrySite']['url'];
        $urlPath = substr($urlPath,0,8)."Syngenta:Syngenta1@".substr($urlPath,8);
        $urlPath = $urlPath.$SCS;
        $this->getSession()->visit($urlPath);
    }

    /** Go to specific second country site page using parameter
     *
     * @Given /^I go to second country site "([^"]*)"$/
     */
    public function iGoToSecondCountrySite($SCS)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params['secondCountrySite']['url'];
        $urlPath = $urlPath.$SCS;
        $this->getSession()->visit($urlPath);
    }


    /**
     * @When I set random IDs to the :file CSV
     */
    public function SetRandomIDtoCSV($file) {
        ini_set("auto_detect_line_endings", true);
        $base_path = $this->getMinkParameter('files_path');
        $file_path = $base_path.$file;
        $csv_handle = fopen($file_path, 'c+');
        $put_lines = array();
        // Read the whole CSV file
        while (($line = fgetcsv($csv_handle,0,";",'"')) !=FALSE){
            $timestamp = time() + rand (1, 1000);
            $put_line = array();
            // For each line of the CSV, read all items
            for ($i = 0 ; $i < count ($line) ; $i++) {
                // Check if currently on first element (the ID) and set it to a timestamp (in order to be unique)
                if ($i==0) {
                    array_push ($put_line, $timestamp);
                }
                else {
                    array_push ($put_line, $line[$i]);
                }
            }
            array_push ($put_lines, $put_line);
        }
        // Clear the file
        ftruncate($csv_handle, 0);
        rewind($csv_handle);
        // Push the edited data to the CSV
        foreach ($put_lines as $line) {
            //if (count($line) > 1) {
            fputcsv($csv_handle, $line, ";");
            // }
        }
        fclose($csv_handle);
    }

    /**
     * Assert the first name of the user by Facebook
     *
     * @When I assert the first name of the user by Facebook
     */
    public function assertFirstNameOfTheUserByFacebook()
    {
        $input = "#edit-field-profile-first-name-und-0-value";
        $value = $this->params['Facebook']['FirstName'];
        $session = $this->getSession();
        $element = $session->getPage()->find('css',$input);
        $text="";
        if(isset($element)) $text=$element->getValue();
        else throw new Exception(sprintf("Element is null"));

        if($text===$value) return true;
        else throw new Exception(sprintf('Value of input : "%s" does not match the text "%s"', $text,$value));
    }

    /**
     * Assert the last name of the user by Facebook
     *
     * @When I assert the last name of the user by Facebook
     */
    public function assertLastNameOfTheUserByFacebook()
    {
        $input = "#edit-field-profile-last-name-und-0-value";
        $value = $this->params['Facebook']['LastName'];
        $session = $this->getSession();
        $element = $session->getPage()->find('css',$input);
        $text="";
        if(isset($element)) $text=$element->getValue();
        else throw new Exception(sprintf("Element is null"));

        if($text===$value) return true;
        else throw new Exception(sprintf('Value of input : "%s" does not match the text "%s"', $text,$value));
    }

    /**
     * Assert the email of the user by Facebook
     *
     * @When I assert the email of the user by Facebook
     */
    public function assertEmailOfTheUserByFacebook()
    {
        $input = "#edit-mail";
        $value = $this->params['Facebook']['user'];
        $session = $this->getSession();
        $element = $session->getPage()->find('css',$input);
        $text="";
        if(isset($element)) $text=$element->getValue();
        else throw new Exception(sprintf("Element is null"));

        if($text===$value) return true;
        else throw new Exception(sprintf('Value of input : "%s" does not match the text "%s"', $text,$value));
    }

    /**
     * Assert the confirm email of the user by Facebook
     *
     * @When I assert the confirm email of the user by Facebook
     */
    public function assertConfirmEmailOfTheUserByFacebook()
    {
        $input = "#edit-conf-mail";
        $value = $this->params['Facebook']['user'];
        $session = $this->getSession();
        $element = $session->getPage()->find('css',$input);
        $text="";
        if(isset($element)) $text=$element->getValue();
        else throw new Exception(sprintf("Element is null"));

        if($text===$value) return true;
        else throw new Exception(sprintf('Value of input : "%s" does not match the text "%s"', $text,$value));
    }

    /**
     * @When I fill in :arg1 with :arg2 parameter
     * @When I fill in :arg1 with :arg2 :arg3 parameter
     */
    public function dasdasdasda($field, $value1, $value2=null)
    {
        if (isset($value2)) {
            $value = $this->params[$value1][$value2];
        }
        else $value = $this->params[$value1];
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /** Assert that I see Facebook's first name and last name on the page
     * @Then I should see Facebook's first name and last name
     */
    public function iShouldSeeFacebookFirstNameLastName()
    {
        $text = $this->params['Facebook']['FirstName']." ".$this->params['Facebook']['LastName'];
        $this->assertSession()->pageTextContains($text);
    }

    /** Assert that I am on My Profile page
     * @Then I should be on My Profile page
     */
    public function iShouldBeOnMyProfilePage()
    {
        $url = $this->getSession()->getCurrentUrl();
        $re = "/\\/user\\/\\d+\\/profile\\/main/";
        preg_match($re, $url, $matches);
        if (count($matches) == 0) {
            throw new Exception(sprintf('You are on the wrong page: %s', $url));
        }
    }

}
