<?php
/**
 * @file MobileApiContext feature context class
 */

use Behat\Gherkin\Node\PyStringNode;
use GuzzleHttp\Client;
use PHPUnit_Framework_Assert as Assertions;

class MobileApiContext extends \Drupal\DrupalExtension\Context\RawDrupalContext implements \Behat\Behat\Context\Context {
  /** @var  \GuzzleHttp\Client */
  protected $client;
  protected $request_body;
  /** @var  GuzzleHttp\Psr7\Response */
  protected $response;
  protected $headers;

  // Mobile API-specific
  protected $timers;
  protected $json;
  protected $current_object_type;
  protected $given_data;

  protected $params;

  public function __construct(array $parameters) {
    $this->client = new Client(['cookies' => TRUE]);
    $this->params = $parameters;
    if (substr($this->params['base_uri'], -1) == '/') {
      // Strip trailing slash when present.
      $this->params['base_uri'] = substr($this->params['base_uri'], 0, strlen($this->params['base_uri']) - 1);
    }
  }


  /**
   * @Given /^I create a random user with "([^"]*)" role$/
   */
  public function iCreateRandomUserWithRole($role)
  {
    $options = array(
      'password' => $this->randomString(),
      // Make sure it's unique
      'mail' => $this->randomString(25) . '_' . time() . '@example.com',
    );
    try
    {
      $this->getDriver()->drush('user-create', array($options['mail']), $options);
      $this->getDriver()->drush('user-add-role', array(
        sprintf('"%s"', $role),
        $options['mail']
      ));
      $this->given_data['internal']['user-email'] = $options['mail'];
      $this->given_data['internal']['user-password'] = $options['password'];
    }
    catch (RuntimeException $e)
    {
      throw new Exception("Email {$options['mail']} is already taken or the drush command user-create cannot be executed.");
    }
  }

  /**
   * Store part of response as internal variable.
   *
   * @param string $parameter response part to address
   * @param string $name variable name
   *
   * @When /^(?:I )?store "([^"]+)" to "([^"]+)" header$/
   */
  public function iRememberHeader($property, $name) {
    $this->headers[$name] = $this->given_data['internal'][$property];
  }

  /**
   * Store part of response as internal variable.
   *
   * @param string $parameter response part to address
   * @param string $name variable name
   *
   * @When /^(?:I )?store "([^"]+)" to "([^"]+)" variable$/
   */
  public function iRememberVariable($property, $name) {
    if (is_object($this->json)) {
      Assertions::assertObjectHasAttribute($property, $this->json);
      $this->given_data['internal'][$name] = $this->json->{$property};
    }
    elseif (is_array($this->json)) {
      Assertions::assertArrayHasKey($property, $this->json);
      $this->given_data['internal'][$name] = $this->json[$property];
    }
    else {
      Assertions::assertTrue(FALSE, 'Variable not found');
    }
  }

  /**
   * Store part of response as internal variable.
   *
   * @param string $parameter response part to address
   * @param string $name variable name
   *
   * @When /^(?:I )?store "([^"]+)" of item #(\d+) to "([^"]+)" variable$/
   */
  public function iRememberVariableFromList($property, $index, $name) {
    Assertions::assertTrue(is_array($this->json));
    Assertions::arrayHasKey($index, $this->json);
    Assertions::assertObjectHasAttribute($property, $this->json[$index]);
    $this->given_data['internal'][$name] = $this->json[$index]->{$property};
  }

  /**
   * Store random string as internal variable.
   *
   * @param string $parameter response part to address
   * @param string $name variable name
   *
   * @When /^(?:I )?store random value to "([^"]+)" variable$/
   */
  public function iRememberRandomValue($name) {
    $this->given_data['internal'][$name] = $this->randomString();
  }


  /**
   * @Given /^that I have request body:$/
   */
  public function iHaveRequestBody(PyStringNode $body) {
    $this->request_body = $body;
  }

  /**
   * Sends HTTP request to specific relative URL.
   *
   * @param string $method request method
   * @param string $url    relative url
   *
   * @When /^(?:that )?(?:I )?send a ([A-Z]+) request to "([^"]+)"$/
   */
  public function iSendARequest($method, $url) {
    $this->sendRequest($method, $url);
  }

  /**
   * @param $entity_type
   * @param $bundle
   *
   * @When /^(?:I )?pick a random ([A-Za-z_-]+) ([A-Za-z_-]+)$/
   */
  public function pickRandomEntity($bundle, $entity_type) {
    $driver = $this->getDrupal()->getDriver();
    if (!$driver->isBootstrapped()) {
      $driver->bootstrap();
    }
    if ($driver instanceof \Drupal\Driver\DrushDriver) {
      $object = $driver->drush('lush-behat-random-entity',
        array($entity_type, $bundle));
    }
    else {
      module_load_include('drush.inc', 'lush_behat');
      $object = drush_lush_behat_random_entity($entity_type, $bundle);
    }
    $this->json = $this->given_data[$entity_type][$bundle] = json_decode($object, TRUE);
  }

  /**
   * Checks that response is an array.
   *
   * @param string $code status code
   *
   * @Then /^(?:the )?property "([^"]+)" should be "([^"]+)"$/
   */
  public function propertyShouldHaveValue($property, $value) {
    if (is_object($this->json)) {
      Assertions::assertEquals($value, $this->json->{$property});
    }
    else {
      Assertions::assertEquals($value, $this->json[$property]);
    }
  }

  /**
   * Checks that response has specific status code.
   *
   * @param string $code status code
   *
   * @Then /^(?:the )?response code should be (\d+)$/
   */
  public function theResponseCodeShouldBe($code)
  {
    $expected = intval($code);
    $actual = intval($this->response->getStatusCode());
    Assertions::assertSame($expected, $actual);
  }

  /**
   * Checks that response has specific content type.
   *
   * @param string $code status code
   *
   * @Then /^(?:the )?response content type should be "([^"]+)"$/
   */
  public function theResponseContentTypeShouldBe($content_type)
  {
    $expected = $content_type;
    $actual = $this->getContentType();
    Assertions::assertSame($expected, $actual);
  }

  /**
   * Checks that response is not empty..
   *
   *
   * @Then /^(?:the )?response should not be empty$/
   */
  public function responseShouldNotBeEmpty(){

    Assertions::assertNotEmpty($this->json);

  }

  /**
   * Checks that response is an array.
   *
   * @param string $code status code
   *
   * @Then /^(?:the )?response should be (?:an )?(array|object)$/
   */
  public function theResponseShouldBe($type)
  {
    if ($type == 'array') {
      Assertions::assertTrue(is_array($this->json));
    }
    else {
      Assertions::assertTrue(is_object($this->json));
    }
  }

  /**
   * Checks that response is an array.
   *
   * @param string $code status code
   *
   * @Then /^(?:the )?response should have property "([^"]+)"$/
   */
  public function theResponseShouldHaveProperty($property) {
    if (is_object($this->json)) {
      Assertions::assertObjectHasAttribute($property, $this->json);
    }
    else {
      Assertions::assertArrayHasKey($property, $this->json);
    }
  }

  /**
   * Prints last response body.
   * @category debug
   *
   * @Then /^print ([a-zA-Z0-9_-]+) ([a-zA-Z0-9_-]+) ([a-zA-Z0-9_-]+)$/
   */
  public function printGivenProperty($bundle, $entity_type, $property) {
    print $this->given_data[$entity_type][$bundle][$property];
  }

  /**
   * Prints last response body.
   * @category debug
   *
   * @Then /^debug print the whole response$/
   */
  public function printResponse() {
    print $this->response->getBody()->getContents();
    if (!empty($this->json)) {
      print_r($this->json);
    }
  }

  protected function replacePlaceholders($matches) {
    return $this->given_data['internal'][$matches[1]];
  }

  protected function getContentType() {
    return $this->response->getHeader('Content-Type')[0];
  }

  protected function sendRequest($method, $url) {
    $url = preg_replace_callback('/%([a-zA-Z_-]+)%/',
      array($this, 'replacePlaceholders'), $url);
    if (array_key_exists('base_uri', $this->params)) {
      $url = $this->params['base_uri'] . $url;
    }
    $options = ['headers' => [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ]];
    if (array_key_exists('api-key', $this->params)) {
      $options['headers']['X-Api-Key'] = $this->params['api-key'];
    }
    if (!empty($this->headers)) {
      $options['headers'] += $this->headers;
    }
    if (in_array($method, ['POST', 'PUT'])) {
      // Replace placeholders
      $options['body'] = preg_replace_callback('/%([a-zA-Z_-]+)%/',
        array($this, 'replacePlaceholders'), $this->request_body);
    }
    $start = microtime(TRUE);
    try {
      $this->response = $this->client->request($method, $url, $options);
    }
    catch (\Guzzle\Http\Exception\BadResponseException $e) {
      $this->response = $e->getResponse();
      // Do not rethrow exceptions.
      // non-200 responses may be totally OK
    }
    $end = microtime(TRUE) - $start;
    print ("Request to $url finished in $end ms");
    if ($this->getContentType() == 'application/json') {
      $this->json = json_decode((string)$this->response->getBody());
    }
    else {
      $this->json = FALSE;
    }
  }

  protected function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters) -1 ;
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength)];
    }
    return $randomString;
  }
}
