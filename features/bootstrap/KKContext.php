<?php
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;

class KKContext extends FeatureContext
{


/** @BeforeScenario */
public function before(BeforeScenarioScope $scope)
{
    $driver = $this->getSession()->getDriver();
   if (get_class($driver) !== 'Behat\Mink\Driver\GoutteDriver'){
    $this->maximizeWindow();
   }
   }

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


    /**
     * Fills in form field with specified id|name|label|value with custom value extracted from yml file which is appended in the end of entered string
     *
     * @When /^(?:|I )param fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     * @When /^(?:|I )param fill in "(?P<field>(?:[^"]|\\")*)" with:$/
     * @When /^(?:|I )param fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function fillParamField($field, $value)
    {

        $param = $this->params['Date']['current'];
        $this->getSession()->getPage()->fillField($field, $param.$value);
    }

    /** Log in Drupal with provided username and password. Used for Jenkins integration
     * @Given /^I login main site as sysadmin$/
     */
    public function iLoginMainSiteAsSysAdmin()
    {
        $this->iLoginMainSite($this->params['mainSite']['sysUser'], $this->params['mainSite']['sysPass']);
    }

    /** Log in Drupal with provided username and password. Used for Jenkins integration
     * @Given /^I login main site as administrator$/
     */
    public function iLoginMainSiteAsAdministrator()
    {
        $this->iLoginMainSite($this->params['mainSite']['administratorUser'], $this->params['mainSite']['administratorPass']);
    }

    /** Log in Drupal with provided username and password. Used for Jenkins integration
     * @Given /^I login main site as editor$/
     */
    public function iLoginMainSiteAsEditor()
    {
        $this->iLoginMainSite($this->params['mainSite']['editorUser'], $this->params['mainSite']['editorPass']);
    }

     /** Log in Drupal with provided username and password. Used for Jenkins integration
     * @Given /^I login main site$/
     */
    public function iLoginMainSite($userName=null, $password=null)
    {
        if (isset($userName)){
            $usern = $userName;
        }
        else{
            $usern = $this->params['mainSite']['user'];
        }
        
        if (isset($password)){
            $passw = $password;
        }
        else{
            $passw = $this->params['mainSite']['pass'];
        }

        $session = $this->getSession();
        $page = $session->getPage();
        $page->fillField("edit-name", $usern);
        $page->fillField("edit-pass", $passw);
        $page->pressButton("edit-submit");
    }

        /** Log in Drupal with provided username and password. Used for Jenkins integration
     * @Given /^I login subSiteOne site$/
     */
    public function iLoginSubSiteOne()
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $usern = $this->params['subSiteOne']['user'];
        $passw = $this->params['subSiteOne']['pass'];
        $page->fillField("edit-name", $usern);
        $page->fillField("edit-pass", $passw);
        $page->pressButton("edit-submit");
    }

        /** Log in Drupal with provided username and password. Used for Jenkins integration
     * @Given /^I login ctax$/
     */
    public function iLoginCtax()
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $usern = $this->params['ctax']['user'];
        $passw = $this->params['ctax']['pass'];
        $page->fillField("edit-name", $usern);
        $page->fillField("edit-pass", $passw);
        $page->pressButton("edit-submit");
    }

        /** Go to specific main site login page using parameters
     * @Given /^I go to main site login$/
     */
    public function gotoMainlogin()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $drupalUser = '/user';
        $loginPath = $this->params['mainSite']['url'];
        $this->getSession()->visit($this->locatePath($loginPath.$drupalUser));

    }

    /** Go to specific subSiteOne login page using parameters
     * @Given /^I go to subSiteOne login$/
     */
    public function goTosubSiteOneLogin()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $drupalUser = '/user';
        $loginPath = $this->params['subSiteOne']['url'];
        $this->getSession()->visit($this->locatePath($loginPath.$drupalUser));

    }

/** Go to specific subSiteTwo login page using parameters
     * @Given /^I go to ctax login$/
     */
    public function goToCtax()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $drupalUser = '/user';
        $loginPath = $this->params['ctax']['url'];
        $this->getSession()->visit($this->locatePath($loginPath.$drupalUser));

    }

    /**
     * Opens specified page on ctax
     *
     * @Given /^(?:|I )am on ctax "(?P<page>[^"]+)"$/
     * @When /^(?:|I )go to ctax "(?P<page>[^"]+)"$/
     */
    public function visitCtax($url)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $visitPath = $this->params['ctax']['url'];
        $path = $visitPath.$url;
        $this->getSession()->visit($path);
    }

    /**
     * Opens specified page on subSiteOne
     *
     * @Given /^(?:|I )am on subSiteOne "(?P<page>[^"]+)"$/
     * @When /^(?:|I )go to subSiteOne "(?P<page>[^"]+)"$/
     */
    public function visitSubSiteOne($url)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $visitPath = $this->params['subSiteOne']['url'];
        $path = $visitPath.$url;
        $this->getSession()->visit($path);
    }


    /**
     * Checks, that page contains specified text with param
     *
     * @Then /^(?:|I )should see param "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageContainsTextParam($text)
    {
    	$param = $this->params['Date']['current'];
        $this->assertSession()->pageTextContains($param.$text);
    }

    /**
     * Checks, that page doesn't contain specified text.
     *
     * @Then /^(?:|I )should not see param "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageNotContainsTextParam($text)
    {
    	$param = $this->params['Date']['current'];
        $this->assertSession()->pageTextNotContains($param.$text);
    }

     /**
     * Clicks link with specified id|title|alt|text and prepended param
     *
     * @When /^(?:|I )follow param "(?P<link>(?:[^"]|\\")*)"$/
     */
    public function clickLinkParam($link)
    {
    	$param = $this->params['Date']['current'];
        $this->getSession()->getPage()->clickLink($param.$link); 
    }

     /**
     * Selects option in select field with specified id|name|label|value with param
     *
     * @When /^(?:|I )select param "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function selectOptionParam($select, $option)
    {
        $param = $this->params['Date']['current'];
        $this->getSession()->getPage()->selectFieldOption($select, $param.$option);
    }

     /**
     * Selects additional option in select field with specified id|name|label|value with param
     *
     * @When /^(?:|I )additionally select param "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function additionallySelectOptionParam($select, $option)
    {
        $param = $this->params['Date']['current'];
        $this->getSession()->getPage()->selectFieldOption($select, $param.$option, true);
    }

     /**
     * Checks, that form field with specified id|name|label|value has specified value with param
     *
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should contain param "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertFieldContains($field, $value)
    {
       $param = $this->params['Date']['current'];
        $this->assertSession()->fieldValueEquals($field, $param.$value);
    }


    /**
     * Opens specified page with param
     *
     * @Given /^(?:|I )param am on "(?P<page>[^"]+)"$/
     * @When /^(?:|I )param go to "(?P<page>[^"]+)"$/
     */
    public function visit($page)
    {
    	$param = $this->params['Date']['current'];
        $exploded = explode("/", $page);
        $prepend = $param.$exploded[2];
        $exploded[2] = $prepend;
        $finalString= implode("/", $exploded);
        $this->getSession()->visit($this->locatePath($finalString));
    }

     /**
   * Attempts to find a link in a table row containing giving text. This is for
   * administrative pages such as the administer content types screen found at
   * `admin/structure/types`. Modified to use parameters
   *
   * @Given I click :link in the param :rowText row
   * @Then I (should )see the :link in the param :rowText row
   */
  public function assertClickInTableRow($link, $rowText) {
    $param = $this->params['Date']['current'];
    $page = $this->getSession()->getPage();
    $rowText = $param.$rowText;
    if ($link = $this->getTableRow($page, $rowText)->findLink($link)) {
      // Click the link and return.
      $link->click();
      return;
    }
    throw new \Exception(sprintf('Found a row containing "%s", but no "%s" link on the page %s', $rowText, $link, $this->getSession()->getCurrentUrl()));
  }
}