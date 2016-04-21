<?php
use Behat\Behat\Tester\Exception\PendingException;

class LushContext extends FeatureContext
{

public function __construct(array $parameters)
    {
      $this->params = $parameters;
    }


/**
     * Save data from provided CSS selector (LUSH SPECIFIC CASE!!!!)
     *
     * @When /^I compare LUSH dev and beta csv file for links "([^"]*)" and csv for ids "([^"]*)" from row "([^"]*)"$/
     */
    public function modifiedcompareLUSH($links_csv,$ids_csv,$row)
    {
        $dev_array=array();
        $beta_array=array();

        $file = fopen($links_csv,"r");
        $processing = 0;
        $counter = 0;
        $limit=10;

        while(! feof($file))
        {
          
          $csv_row = fgetcsv($file);
          $counter++;
          if ($counter < $row+1)
          {
            continue;
          }
          if ($processing == $limit)
          {
            break;
          }
          $processing++;

          $product_type = $csv_row[0];
          $beta_site = $csv_row[1];
          $dev_site = $csv_row[2];

          if($product_type == "commerce_product_stand")
          {
              $session = $this->getSession();
              $page = $session->getPage();

              $session->visit("http://lush:t-shirt-phone-pen-table@beta.lush.co.uk/user/login");
              $page->fillField("edit-name", "biser");
              $page->fillField("edit-pass", "123456Pm1");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($beta_site);

              $file_ids = fopen($ids_csv,"r");
              while(! feof($file_ids))
              {
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                
                    array_push($beta_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;
                  }

                 
                }
               $element=$this->getSession()->getPage()->find('css','#edit-field-hazardous-volume-und-0-value');
                    if(isset($element))
                    {
                    if($element->getValue()=='')
                    {
                      array_push($beta_array,0);
                    }
                    else
                    {
                      array_push($beta_array, (int)$element->getValue()); 
                    }
                    }
                    else
                    {
                      array_push($beta_array,0);
                    }
              fclose($file_ids);

              #Logout
              $session->visit("http://beta.lush.co.uk/user/logout");

              #STEP 2 LUSH WEBSITE DEV
              $session->visit("http://lush:zom-dars-u@lushwebukdev.prod.acquia-sites.com/user/login");
              $page->fillField("edit-name", "admin");
              $page->fillField("edit-pass", "lush_website_uk");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($dev_site);
              
              $file_ids = fopen($ids_csv,"r");
              while(! feof($file_ids))
              {
                $ids_csv_row = fgetcsv($file_ids);
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                
                    array_push($dev_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;
                  }
                  
              }
              $element=$this->getSession()->getPage()->find('css','#edit-field-hazardous-volume-und-0-value');
                    if(isset($element))
                    {
                    if($element->getValue()=='')
                    {
                      array_push($dev_array,0);
                    }
                    else
                    {
                      array_push($dev_array, (int)$element->getValue()); 
                    }
                    }
                    else
                    {
                      array_push($dev_array,0);
                    }
              fclose($file_ids);

              #Logout
              $session->visit("http://lushwebukdev.prod.acquia-sites.com/user/logout");

              for($i=0;$i<sizeof($beta_array);$i++) 
              {
                  if($beta_array[$i] !== $dev_array[$i])
                  {
                    echo "The problem is on ".$beta_site." and ".$dev_site."\n\n";
                    echo $i;
                    switch ($i) 
                    {
                      case 0:
                        echo "Different SKU - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 1:
                        echo "Different Title - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 2:
                        echo "Different Image - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 3:
                        echo "Different Price - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 4:
                        echo "Different Price KG - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 5:
                        echo "Different VAT - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 6:
                        echo "Different Unit of Measure - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 7:
                        echo "Different Size - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 8:
                        echo "Different Gross weight - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 9:
                        echo "Different Gross weight - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 10:
                        echo "Different Net weight - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 11:
                        echo "Different Net weight - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 12:
                        echo "Different Fresh - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 13:
                        echo "Different Limited - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 14:
                        echo "Different Made to order - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 15:
                        echo "Different Time Limited - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 16:
                        echo "Different Yes - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 17:
                        echo "Different Status - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 18:
                        echo "Different Sale price - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 19:
                        echo "Different Sale quantity - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;     
                      case 20:
                        echo "Different Hazardous volume - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;      
                    }
                  }
                
              }
              unset($beta_array);
              $beta_array = array();
              unset($dev_array);
              $dev_array = array();
        }
      }
      fclose($file);
}

 /**
   * Sets an id for the first iframe situated in the element specified by id.
   * Needed when wanting to access elements situated in an iframe without identifier.
   *
   * @When the iframe in element :arg1 has id :arg2
   */
/*  public function theIframeInElementHasId($element_id, $iframe_id) {
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
  }*/

   /**
     * @Then /^I fill in wysiwyg on field "([^"]*)" with "([^"]*)"$/
     */
    public function iFillInWysiwygOnFieldWith($arg, $arg2)
    {
        $this->getSession()->executeScript("CKEDITOR.instances.$arg.setData(\"$arg2\");");
    }


   /**
   * Sets an id on img fields
   * 
   * @Given /^the img in element "(?P<element>[^"]*)" has class "(?P<id>[^"]*)"$/
   */
  public function setIdImgFields($element_id, $specific_id) {
    $function = <<<JS
    (function(){
    var elem = document.getElementById("$element_id");
    var iframes = elem.getElementsByTagName('img');
    var f;
    var i;
    for(i=0;i<iframes.length;i++)
    {
     f = iframes[i];
     f.id = "$specific_id";
    }
    })()
JS;
    try {
      $this->getSession()->executeScript($function);
    }
    catch(Exception $e) {
      throw new \Exception(sprintf('No images found in the element "%s" on the page "%s".', $element_id, $this->getSession()->getCurrentUrl()));
    }
  }

   /**
   * Sets an class on input fields
   * 
   * @Given /^the input in element "(?P<element>[^"]*)" has class "(?P<id>[^"]*)"$/
   */
  public function setClassInputFields($element_id, $specific_id) {
    $function = <<<JS
    (function(){
    var elem = document.getElementById("$element_id");
    var iframes = elem.getElementsByTagName('input');
    var f;
    var i;
    for(i=0;i<iframes.length;i++)
    {
     f = iframes[i];
     if(f.type=="text")
     f.classList.add("$specific_id");
    }
    })()
JS;
    try {
      $this->getSession()->executeScript($function);
    }
    catch(Exception $e) {
      throw new \Exception(sprintf('No images found in the element "%s" on the page "%s".', $element_id, $this->getSession()->getCurrentUrl()));
    }
  }

   /**
   * Sets an class on table
   * 
   * @Given /^the table in element "(?P<element>[^"]*)" has class "(?P<id>[^"]*)"$/
   */
  public function setClassTable($element_class, $specific_id) {
    $function = <<<JS
    (function(){
    var div_table = document.getElementsByClassName("$element_class");
    var table = div_table[0].getElementsByTagName("tr");
    var f;
    var x;
    var i;
    for(i=0;i<table.length;i++)
    {
     f = table[i];
     f.classList.add("$specific_id");
    }
    for(i=0;i<table.length;i++)
    {
    tds=table[i].getElementsByTagName('td');
    for(j=0;j<tds.length;j++)
   {
     x = tds[j];
     x.classList.add("$specific_id"+"_"+"1");
    }
}
    })()
JS;
    try {
    $this->getSession()->executeScript($function);
    }
    catch(Exception $e) {
      throw new \Exception(sprintf('No table found in the element "%s" on the page "%s".', $element_class, $this->getSession()->getCurrentUrl()));
    }

  }


/** Get text from element
 * @Given /^Get text from class and tag "([^"]*)"$/
 */
public function printElementText($locator)
{
   $session=$this->getSession();
   $page=$session->getPage();
   $element=$page->find('css',$locator);

  return $element->getText();

}


/** Count appearance of item with css selector
 * @Given /^Count appearance of "([^"]*)"$/
 */
public function CountAppOf($locator)
{
   $session=$this->getSession();
   $page=$session->getPage();
   $element=$page->findAll('css',$locator);

  return count($element);

}



    private function checkFindEmpty($element, $method) {
      if (isset($element)) {
          $text=$element->{$method}();
          $text=trim($text, '<p>');
          $text=trim($text, '</');
          return $text;
      }
      else {
        return '';
      }
    }

  public function returnTextOfTdWithClass($locator)
  {
    $session=$this->getSession();
    $page=$session->getPage();
    $element=$page->find('css','td[class="'.$locator.'"]');
    if(isset($element))
    {
      return $element->getText();
    }
    else
    {
      throw new \Exception(sprintf('The element with class "%s" does not exist', $locator));
    }
  }

/**
     * Save data from provided CSS selector (LUSH SPECIFIC CASE!!!!)
     *
     * @When /^I compare LUSH dev and beta product standart csv file for links "([^"]*)" and csv for ids "([^"]*)" from row "([^"]*)"$/
     */
    public function productDisplayCompareLUSH($links_csv,$ids_csv,$row)
    {
        $dev_array=array();
        $beta_array=array();

        $file = fopen($links_csv,"r");
        $processing = 0;
        $counter = 0;
        $limit=3;

        while(! feof($file))
        {
          
          $csv_row = fgetcsv($file);
          $counter++;
          if ($counter < $row+1)
          {
            continue;
          }
          if ($processing == $limit)
          {
            break;
          }
          $processing++;

          $product_type = $csv_row[0];
          $beta_site = $csv_row[1];
          $dev_site = $csv_row[2];

          
              $session = $this->getSession();
              $page = $session->getPage();
              
              $session->visit("http://lush:t-shirt-phone-pen-table@beta.lush.co.uk/user/login");
              $page->fillField("edit-name", "biser");
              $page->fillField("edit-pass", "123456Pm1");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($beta_site);


              $file_ids = fopen($ids_csv,"r");
              $ids_row_count=0;
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                
                    array_push($beta_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'text_area':
                
                    array_push($beta_array,$this->paragraphTrim($this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getText')));
                    
                    break;

                  case 'select':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                
                    break;  

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;

                  case 'select_field':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;

                  case 'review':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;  
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;

                  case 'td_entity':
                    
                    $text=$this->returnTextOfTdWithClass($field_id);
                    array_push($beta_array,$text);
                    
                    break;
                  }


                 if($ids_row_count==3)
                 {
                  $this->iClickOnTheElement("#edit-field-product-und-entities-0-actions-ief-entity-edit");                  
                 }                           

                 if($ids_row_count==11)
                 {
                   
                    $this->theIframeInElementHasId("cke_edit-body-und-0-value","migration_id");
                    $this->iSwithToIframe("migration_id");
                    $text=$this->printElementText(".cke_editable p");
                    $this->iSwitchBackToOriginalWindow();
                    array_push($beta_array,$text);
                 }

                if($ids_row_count==14)
                {  
                  $this->iClickOnTheElement("#wysiwyg-toggle-edit-field-how-to-use-und-0-value");
                }

                if($ids_row_count==18)
                {
                  $this->iClickOnTheText("Revision information");
                }

                if($ids_row_count==20)
                {
                  $this->iClickOnTheText("Comment settings");
                }
                if($ids_row_count==23)
                {
                  $this->iClickOnTheText("Authoring information");
                }
                if($ids_row_count==25)
                {
                  $this->iClickOnTheText("Publishing options");
                }
                if($ids_row_count==28)
                {
                  $this->iClickOnTheText("Media");
                  $this->setIdImgFields("node_product_display_form_group_media","migration_img_id");
                  sleep(2);
                  array_push($beta_array,$this->CountAppOf("#migration_img_id"));
                }
                if($ids_row_count==29)
                {
                  $this->iClickOnTheText("Categorisation");
                }
                if($ids_row_count==35)
                {
                  $this->iClickOnTheText("Special attributes");
                }
                if($ids_row_count==43)
                {
                  $this->iClickOnTheText("Relationships");
                }
                if($ids_row_count==46)
                {
                  $finded_input=$this->getSession()->getPage()->find('css',"#edit-field-related-products-und-0-target-id");               
                  array_push($beta_array,preg_replace('/\d+/', '', $finded_input->getValue()));
                }
                if($ids_row_count==47)
                {
                 $this->iClickOnTheText("Kitchen"); 
                }
                if($ids_row_count==56)
                {
                  $this->iClickOnTheText("Availability");
                }

               

            } 

                 fclose($file_ids);

        
           #Logout
              $session->visit("http://beta.lush.co.uk/user/logout");
              sleep(2);
           #STEP 2 LUSH WEBSITE DEV
              $session->visit("http://lush:zom-dars-u@content.lush.com/user/");
              $page->fillField("edit-name", "lush_admin");
              $page->fillField("edit-pass", "tFu-e8E-n6R-frm");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($dev_site);

              $file_ids = fopen("files/ids_displays_dev.csv","r");
               $ids_row_count=0;
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                    
                    array_push($dev_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'text_area':
                
                    array_push($dev_array, $this->paragraphTrim($this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getText')));
                    
                    break;

                  case 'select':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                
                    break;  

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;

                  case 'select_field':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;
                  

                  case 'review':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;  
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;

                  case 'td_entity':
                    
                    $text=$this->returnTextOfTdWithClass($field_id);
                    array_push($dev_array,$text);
                    
                    break;
                  }

                if($ids_row_count==3)
                {
                 $this->iClickOnTheElement("#edit-field-product-und-entities-0-actions-ief-entity-edit");  
                }            
                if($ids_row_count==10)
                {
                 $this->iClickOnTheElement("#switch_edit-body-und-0-value");
                }               
                if($ids_row_count==15)
                {  
                  $this->iClickOnTheElement("#switch_edit-field-how-to-use-und-0-value");
                }
                if($ids_row_count==20)
                {
                  $this->iClickOnTheText("Comment settings");
                }
                if($ids_row_count==23)
                {
                  $this->iClickOnTheText("Authoring information");
                }
                if($ids_row_count==25)
                {
                  $this->iClickOnTheText("Publishing options");
                }
                if($ids_row_count==28)
                {
                  $this->iClickOnTheText("Media");
                  $this->setIdImgFields("edit-group_media","migration_img_id");
                  sleep(2);
                  array_push($dev_array,$this->CountAppOf("#migration_img_id"));
                }
                if($ids_row_count==29)
                {
                  $this->iClickOnTheText("Categorisation");
                }
                if($ids_row_count==35)
                {
                  $this->iClickOnTheText("Special attributes");
                }
                if($ids_row_count==43)
                {
                  $this->iClickOnTheText("Relationships");
                }
                if($ids_row_count==46)
                {
                  $finded_input=$this->getSession()->getPage()->find('css',"#edit-field-related-products-und-0-target-id");               
                  array_push($dev_array,preg_replace('/\d+/', '', $finded_input->getValue()));
                }
                if($ids_row_count==47)
                {
                 $this->iClickOnTheText("Kitchen"); 
                }
                if($ids_row_count==56)
                {
                  $this->iClickOnTheText("Availability");
                }


            } 
            $session->visit("http://content.lush.com/user/logout");
            sleep(2);
            
             for($i=0;$i<sizeof($beta_array);$i++) 
              {
                
                  if($beta_array[$i] !== $dev_array[$i])
                  {
                    echo "The problem is on ".$beta_site." and ".$dev_site."\n\n";
                    switch ($i) 
                    {
                      case 0:
                        echo "Different Title - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 1:
                        echo "Different Category - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 2:
                        echo "Different Image - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 3:
                        echo "Different Entity Title - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 4:
                        echo "Different Entity SKU - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 5:
                        echo "Different Entity Price - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 6:
                        echo "Different Entity Status - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 7:
                        echo "Different Strapline - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 8:
                        echo "Different Summary - \n Beta site =".$beta_array[$i]."\n Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 9:
                        echo "Different Product Discription - \n Beta site =".$beta_array[$i]."\n Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 10:
                        echo "Different Text Format - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 11:      
                        echo "Different Product Type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 12:
                        echo "Different How to use - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 13:
                        echo "Different How to use Format - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 14:
                        echo "Different Excluded from search - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 15:
                        echo "Different Revision - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 16:

                        echo "Different Comments 1 - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 17:
                        echo "Different Comments 2 - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 18:
                        echo "Different Created Date - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 19:
                        echo "Different Promote - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;     
                      case 20:
                        echo "Different Sticky - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 21:
                        echo "Different Number of Media - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 22:
                        echo "Different Feel - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 23:
                        echo "Different Form - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;    
                      case 24:
                        echo "Different Colour - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 25:
                        echo "Different Packaging type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 26:
                        echo "Different Scent - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 27:
                        echo "Different Certification type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 28:
                        echo "Different Preservatives free - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 29:
                        echo "Different Preservatives free - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;   
                      case 30:
                        echo "Different Preservatives free - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 31:
                        echo "Different 100% Natural   - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 32:
                        echo "Different 100% Natural   - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 33:
                        echo "Different 100% Natural   - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 34:
                        echo "Different Featured review   - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 35:
                        echo "Different Featured article  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 36:
                        echo "Different Related  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 37:
                        echo "Different Kitchen  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 38:
                        echo "Different Chef  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 39:
                        echo "Different Kitchen status  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 40:
                        echo "Different Kitchen Sticker - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 41:
                        echo "Different Google Title  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 42:
                        echo "Different Google URL  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 43:
                        echo "Different Kitchen Date  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 44:
                        echo "Different Lead time  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 45:
                        echo "Different Currently unavailable  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 46:
                        echo "Different Excluded from discount - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 47:
                        echo "Different Seasonal discount  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                               
                    }
                  }
                
              }

              unset($beta_array);
              $beta_array = array();
              unset($dev_array);
              $dev_array = array();
          }
          #Logout
              
           fclose($file_ids);
            
      fclose($file);

}

    private function checkFindEmptyAddresses($element, $method) {
      if (isset($element)) {

          $text=$element->{$method}();
           $text=trim($text,"</p>");
          return $text;
      }
      else {
        return '';
      }
    }


/**
     * Save data from provided CSS selector (LUSH SPECIFIC CASE!!!!)
     *
     * @When /^I compare LUSH addresses dev and beta product standart csv file for links "([^"]*)" and csv for ids "([^"]*)" from row "([^"]*)"$/
     */
    public function addressesDisplayCompareLUSH($links_csv,$ids_csv,$row)
    {
        $dev_array=array();
        $beta_array=array();

        $file = fopen($links_csv,"r");
        $processing = 0;
        $counter = 0;
        $limit=1;

        while(! feof($file))
        {
          
          $csv_row = fgetcsv($file);
          $counter++;
          if ($counter < $row+1)
          {
            continue;
          }
          if ($processing == $limit)
          {
            break;
          }
          $processing++;

          $product_type = $csv_row[0];
          $beta_site = $csv_row[1];
          $dev_site = $csv_row[2];

          
              $session = $this->getSession();
              $page = $session->getPage();

              $session->visit("http://lush:t-shirt-phone-pen-table@beta.lush.co.uk/user/login");
              $page->fillField("edit-name", "biser");
              $page->fillField("edit-pass", "123456Pm1");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($beta_site);

              $element=$this->getSession()->getPage()->find('css',"#edit-commerce-customer-address-und-0-country-select-gb");
               if(isset($element)&&!$element->getAttribute('checked'))
              {
                  $this->iClickOnTheElement("#edit-commerce-customer-address-und-0-country-select-gb");   
                  sleep(2); 
              }
              else
              {
                sleep(3);
                $this->iClickOnTheElement("#edit-commerce-customer-address-und-0-country-select-gb");    
                sleep(2);
              }
              $file_ids = fopen($ids_csv,"r");
              $ids_row_count=0;

              while(! feof($file_ids))
              {
                if($ids_row_count==0)
                {
                 
                 $this->iClickOnTheElement("#commerce_customer_profile_billing_commerce_customer_address_und_0-postcodeanywhere_cancel");
                 sleep(2);
                }
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type)
                 {
                  case 'input_text':
                
                    array_push($beta_array, $this->checkFindEmptyAddresses($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;
                  }
              
                if($ids_row_count==11)
                {
                  $this->iClickOnTheText("International");
                  sleep(3);
                }

                if($ids_row_count==20)
                {
                  $this->iClickOnTheText("Status");
                 
                }
               

            } 

                 fclose($file_ids);

        
           #Logout
              $session->visit("http://beta.lush.co.uk/user/logout");

              #STEP 2 LUSH WEBSITE DEV
              $session->visit("http://lush:zom-dars-u@lushwebukdev.prod.acquia-sites.com/user/login");
              $page->fillField("edit-name", "admin");
              $page->fillField("edit-pass", "lush_website_uk");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($dev_site);

              $element=$this->getSession()->getPage()->find('css',"#edit-commerce-customer-address-und-0-country-select-gb");
               if(isset($element)&&!$element->getAttribute('checked'))
              {
                  $this->iClickOnTheElement("#edit-commerce-customer-address-und-0-country-select-gb");   
                  sleep(2); 
              }
              else
              {
                sleep(3);
                $this->iClickOnTheElement("#edit-commerce-customer-address-und-0-country-select-gb");    
                sleep(2);
              }

               $file_ids = fopen($ids_csv,"r");
               $ids_row_count=0;
              
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type)
                 {
                  case 'input_text':
                
                    array_push($dev_array, $this->checkFindEmptyAddresses($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;
                  }
              
                if($ids_row_count==11)
                {
                  $this->iClickOnTheText("International");
                  sleep(3);
                }

                if($ids_row_count==20)
                {
                  $this->iClickOnTheText("Status");
                 
                }
               
              }
               fclose($file_ids);
                #Logout
              $session->visit("http://lushwebukdev.prod.acquia-sites.com/user/logout");
            
           }
           
              for($i=0;$i<sizeof($beta_array);$i++) 
              {
                
                  if($beta_array[$i] !== $dev_array[$i])
                  {
                    echo "The problem is on ".$beta_site." and ".$dev_site."\n\n";
                    switch ($i) 
                    {
                      case 0:
                        echo "Different UK - First Name - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 1:
                        echo "Different UK - Last Name - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 2:
                        echo "Different UK - Company - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 3:
                        echo "Different UK - Flat No - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 4:
                        echo "Different UK - House - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 5:
                        echo "Different UK - Address 1 - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 6:
                        echo "Different UK - Town/City - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 7:
                        echo "Different UK - County - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 8:
                        echo "Different UK - Postcode - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 9:
                        echo "Different UK - Telephone - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 10:
                        echo "Different Country - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 11:      
                         echo "Different INT - First Name - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";                        
                        break;
                      case 12:
                        echo "Different INT - Last Name - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 13:
                        echo "Different INT - Company - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 14:
                        echo "Different INT - Street - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 15:
                        echo "Different INT - Address 2 - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 16:

                        echo "Different INT - ZIP code - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 17:
                        echo "Different INT - City - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 18:
                        echo "Different INT - Telephone - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 19:
                        echo "Different Owned By - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;     
                      case 20:
                        echo "Different Status Active - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 21:
                        echo "Different Status Disabled - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                     
                    }
                  }
                
              }
              
              unset($beta_array);
              $beta_array = array();
              unset($dev_array);
              $dev_array = array();
         
            
      fclose($file);

}

  private function paragraphTrim($text)
  {
    $text=preg_replace('~</?p[^>]*>~', '', $text);
    $text=preg_replace('~&nbsp;~', " " , $text);
    $text=preg_replace('~&rsquo;~',"’", $text);
    $text=preg_replace('/  +/'," ",$text);
    return $text;
  }



/**
     * Save data from provided CSS selector (LUSH SPECIFIC CASE!!!!)
     *
     * @When /^I compare LUSH ingredient dev and beta product standart csv file for links "([^"]*)" and csv for ids "([^"]*)" from row "([^"]*)"$/
     */
    public function ingredientCompareLUSH($links_csv,$ids_csv,$row)
    {
        $dev_array=array();
        $beta_array=array();

        $file = fopen($links_csv,"r");
        $processing = 0;
        $counter = 0;
        $limit=1;

        while(! feof($file))
        {
          
          $csv_row = fgetcsv($file);
          $counter++;
          if ($counter < $row+1)
          {
            continue;
          }
          if ($processing == $limit)
          {
            break;
          }
          $processing++;

          $product_type = $csv_row[0];
          $beta_site = $csv_row[1];
          $dev_site = $csv_row[2];

          
              $session = $this->getSession();
              $page = $session->getPage();

              $session->visit("http://lush:t-shirt-phone-pen-table@beta.lush.co.uk/user/login");
              $page->fillField("edit-name", "biser");
              $page->fillField("edit-pass", "123456Pm1");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($beta_site);


              $file_ids = fopen($ids_csv,"r");
              $ids_row_count=0;
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                
                    array_push($beta_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'text_area':
                      
                     array_push($beta_array, $this->paragraphTrim($this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getText')));
                  
                    break;

                  case 'select':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                
                    break;  

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break; 
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {

                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;
                  }

                if($ids_row_count==3)
                {
                  $element=$this->getSession()->getPage()->findAll("css",".fid");
                  if(isset($element))
                  {
                    foreach ($element as $elem)
                    {
                      if($elem->getAttribute("name")=="field_hero_image[und][0][fid]")
                      {
                        if($elem->getValue()=="0")
                        {
                          array_push($beta_array,$elem->getValue());
                        }
                        else
                        {
                          $labels=$this->getSession()->getPage()->findAll("css",".media-filename");
                          array_push($beta_array,$labels[1]->getText());
                          
                        }
                      }
                    }
                  }
                  
                }



                if($ids_row_count==8)
                {
                  $this->iClickOnTheText("Revision information");
                }
                if($ids_row_count==11)
                {
                  $this->iClickOnTheText("Comment settings");
                 
                }
                if($ids_row_count==14)
                {
                  $this->iClickOnTheText("Authoring information");
                }
                if($ids_row_count==17)
                {
                  $this->iClickOnTheText("Publishing options");
                }
                if($ids_row_count==20)
                {
                  $this->iClickOnTheText("Harvest");
                }
                
                if($ids_row_count==25)
                {
                  $this->iClickOnTheText("Finer detail");
                }

                if($ids_row_count==29)
                {
                   $this->theIframeInElementHasId("cke_edit-body-und-0-value","migration_id");
                    $this->iSwithToIframe("migration_id");
                    $texts=$this->getSession()->getPage()->findAll("css",".cke_editable p");
                    foreach ($texts as $text) 
                    {
                      if($text->getText()!="")
                      array_push($beta_array,$text->getText());
                    }
                    $this->iSwitchBackToOriginalWindow();
                }

            } 

                 fclose($file_ids);

        
           #Logout
              $session->visit("http://beta.lush.co.uk/user/logout");

           #STEP 2 LUSH WEBSITE DEV
              $session->visit("http://lush:zom-dars-u@lushwebukdev.prod.acquia-sites.com/user/login");
              $page->fillField("edit-name", "admin");
              $page->fillField("edit-pass", "lush_website_uk");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($dev_site);

              $file_ids = fopen($ids_csv,"r");
              $ids_row_count=0;
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                if($ids_row_count==9)
                {
                  $field_type="radio_button";
                  $field_id="#edit-revision-operation-2";
                }

                switch ($field_type) {
                  case 'input_text':
                
                    array_push($dev_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'text_area':
                
                    array_push($dev_array, $this->paragraphTrim($this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getText')));
                    
                    break;

                  case 'select':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                
                    break;  

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break; 
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {

                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;
                  }

                if($ids_row_count==3)
                {
                  $element=$this->getSession()->getPage()->findAll("css",".fid");
                  if(isset($element))
                  {
                    foreach ($element as $elem)
                    {
                      if($elem->getAttribute("name")=="field_hero_image[und][0][fid]")
                      {
                        if($elem->getValue()=="0")
                        {
                          array_push($dev_array,$elem->getValue());
                        }
                        else
                        {
                          $labels=$this->getSession()->getPage()->findAll("css",".media-filename");
                          array_push($dev_array,$labels[1]->getText());
                          
                        }
                      }
                    }
                  }
                  
                }



                if($ids_row_count==8)
                {
                  $this->iClickOnTheText("Revision information");
                }
                if($ids_row_count==11)
                {
                  $this->iClickOnTheText("Comment settings");
                 
                }
                if($ids_row_count==14)
                {
                  $this->iClickOnTheText("Authoring information");
                }
                if($ids_row_count==17)
                {
                  $this->iClickOnTheText("Publishing options");
                }
                 

                if($ids_row_count==21)
                {
                  $this->iClickOnTheText("Harvest");
                }
                
                if($ids_row_count==25)
                {
                  $this->iClickOnTheText("Finer detail");
                  $this->iClickOnTheElement("#switch_edit-body-und-0-value");
                }

                if($ids_row_count==29)
                {
                  $this->iClickOnTheElement("#switch_edit-body-und-0-value");

                    $this->theIframeInElementHasId("cke_edit-body-und-0-value","migration_id");
                    $this->iSwithToIframe("migration_id");
                    $texts=$this->getSession()->getPage()->findAll("css",".cke_editable p");
                    foreach ($texts as $text) 
                    {
                      if($text->getText()!="")
                      array_push($dev_array,$text->getText());
                    }
                    $this->iSwitchBackToOriginalWindow();
                }


            } 

                 fclose($file_ids);
            
            $session->visit("http://lushwebukdev.prod.acquia-sites.com/user/logout");

             for($i=0;$i<sizeof($beta_array);$i++) 
              {
                
                  if($beta_array[$i] !== $dev_array[$i])
                  {
                    echo "The problem is on ".$beta_site." and ".$dev_site."\n\n";
                    switch ($i) 
                    {
                      case 0:
                        echo "Different Title - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 1:
                        echo "Different Image - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 2:
                        echo "Different Hero Image - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 3:
                        echo "Different Benefit - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 4:
                        echo "Different Ingredient type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 5:
                        echo "Different Display type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 6:
                        echo "Different Ralated products - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 7:
                        echo "Different Revision - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 8:
                        echo "Different Revision log message - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 9:
                        echo "Different Comments - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 10:
                        echo "Different Comments - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 11:      
                        echo "Different Author name - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";   
                        break;
                      case 12:
                        echo "Different Author date - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 13:
                        echo "Different Promote - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 14:
                        echo "Different Sticky - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 15:
                        echo "Different Origin  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 16:
                        echo "Different Latitude - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 17:
                        echo "Different Longitude - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 18:
                        echo "Different Time of Harvest - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;    
                      case 19:
                        echo "Different Latin name - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 20:
                        echo "Different Strapline - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 21:
                        echo "Different Summary - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 22:
                        echo "Different Body - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 23:
                        echo "Different Body - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;    
                      case 24:
                        echo "Different Text format - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 25:
                        echo "Different Pull Quote - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;   
                      case 26:
                        echo "Different Related articles - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      
                               
                    }
                  }
                
              }

            unset($beta_array);
              $beta_array = array();
              unset($dev_array);
              $dev_array = array();
          #Logout
              
          # fclose($file_ids);
         }
            
      fclose($file);

}
/**
     * Save data from provided CSS selector (LUSH SPECIFIC CASE!!!!)
     *
     * @When /^I create URLs from csv "([^"]*)"$/
     */
public function createCSVurls ($csv)
{
  $file_ids=fopen($csv,"r");
   $new_file=fopen("files/urls_article.csv","w");
  while(!feof($file_ids))
  {
    $ids_csv_row = fgetcsv($file_ids); 
    $source=$ids_csv_row[0];
    $destination=$ids_csv_row[1];
   
    $urls_array=array();
    $urls_array[0]="http://beta.lush.co.uk/node/".$source."/edit";
    $urls_array[1]="http://content.lush.com/node/".$destination."/edit";
    fputcsv($new_file,$urls_array);
    
  }
  fclose($new_file);
  fclose($file_ids);
}


/**
     * Save data from provided CSS selector (LUSH SPECIFIC CASE!!!!)
     *
     * @When /^I compare LUSH dev and beta orders csv file for links "([^"]*)" and csv for ids "([^"]*)" from row "([^"]*)"$/
     */
    public function OrdersCompareLUSH($links_csv,$ids_csv,$row)
    {
        $dev_array=array();
        $beta_array=array();

        $file = fopen($links_csv,"r");
        $processing = 0;
        $counter = 0;
        $limit=1;

        while(! feof($file))
        {
          
          $csv_row = fgetcsv($file);
          $counter++;
          if ($counter < $row+1)
          {
            continue;
          }
          if ($processing == $limit)
          {
            break;
          }
          $processing++;

          $product_type = $csv_row[0];
          $beta_site = $csv_row[1];
          $dev_site = $csv_row[2];

          
              $session = $this->getSession();
              $page = $session->getPage();
              
              $session->visit("http://lush:t-shirt-phone-pen-table@beta.lush.co.uk/user/login");
              $page->fillField("edit-name", "biser");
              $page->fillField("edit-pass", "123456Pm1");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($beta_site);
              sleep(2);
              $this->assertRegionLinkFollow("Billing address");
              $this->iClickOnTheElement("#edit-commerce-customer-billing-und-profiles-0-commerce-customer-address-und-0-country-select-gb");
              $this->iClickOnTheElement("#commerce_customer_profile_billing_commerce_customer_address_und_0-postcodeanywhere_cancel");
              $file_ids = fopen($ids_csv,"r");
              $ids_row_count=0;
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                
                    array_push($beta_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'text_area':
                
                    array_push($beta_array,$this->paragraphTrim($this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getText')));
                    
                    break;

                  case 'select':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                
                    break;  

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;

                  case 'select_field':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;

                  case 'review':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;  
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;

                  case 'td_entity':
                    
                    $text=$this->returnTextOfTdWithClass($field_id);
                    array_push($beta_array,$text);
                    
                    break;
                  }


                 if($ids_row_count==10)
                 {
                  $this->iClickOnTheElement("#edit-commerce-customer-billing-und-profiles-0-commerce-customer-address-und-0-country-select-us");
                  sleep(3);              
                 }                           

                 if($ids_row_count==19)
                 {
                  $this->iClickOnTheText("Shipping address");  
                  $this->iClickOnTheText("UK (excl. Channel Islands) ");
                  sleep(2);
                  $this->iClickOnTheElement("#commerce_customer_profile_shipping_commerce_customer_address_und_0-postcodeanywhere_cancel");
                 }

                if($ids_row_count==29)
                {  
                  $this->iClickOnTheElement("#edit-commerce-customer-shipping-und-profiles-0-commerce-customer-address-und-0-country-select-us");
                  sleep(3);
                }

                if($ids_row_count==38)
                {
                  $this->iClickOnTheText("Gift message");
                }

                if($ids_row_count==40)
                {
                  $this->iClickOnTheText("Delivery message");
                }
                if($ids_row_count==42)
                {
                  $this->iClickOnTheText("Flags");
                }
                if($ids_row_count==49)
                {
                  $this->iClickOnTheText("Tracking");
                }
                if($ids_row_count==55)
                {
                  $this->iClickOnTheText("User information");
                }
                if($ids_row_count==58)
                {
                  $this->iClickOnTheText("Order history");
                } 

                  if($ids_row_count==61)
                {
                  $counter=0;
                  $div_with_table=$this->getSession()->getPage()->find("css","#line-item-manager");
                  $this->setClassTable("sticky-enabled tableheader-processed sticky-table","row_id");
                  $tds=$this->getSession()->getPage()->findAll("css",".row_id_1");
                  foreach($tds as $td)
                  {
                   if($td->getText()!="") {array_push($dev_array,$td->getText());$counter++;}
                   
                  }
               
                } 

            } 

                 fclose($file_ids);

        
           #Logout
              $session->visit("http://beta.lush.co.uk/user/logout");
              sleep(1);
              /*
           #STEP 2 LUSH WEBSITE DEV
              $session->visit("http://lush:zom-dars-u@lushukos.prod.acquia-sites.com/user/");
              $page->fillField("edit-name", "lush_admin");
              $page->fillField("edit-pass", "usV-3wb-k2D-oCr");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($dev_site);

              $file_ids = fopen($ids_csv,"r");
               $ids_row_count=0;
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                    
                    array_push($dev_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'text_area':
                
                    array_push($dev_array, $this->paragraphTrim($this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getText')));
                    
                    break;

                  case 'select':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                
                    break;  

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;

                  case 'select_field':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;
                  

                  case 'review':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;  
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;

                  case 'td_entity':
                    
                    $text=$this->returnTextOfTdWithClass($field_id);
                    array_push($dev_array,$text);
                    
                    break;
                  }

                if($ids_row_count==10)
                {
                 $this->iClickOnTheElement("#edit-commerce-customer-billing-und-profiles-0-commerce-customer-address-und-0-country-select-us");

                }               
                if($ids_row_count==28)
                {  
                  $this->iClickOnTheElement("#edit-commerce-customer-shipping-und-profiles-0-commerce-customer-address-und-0-country-select-us");
                }
                if($ids_row_count==49)
                {
                  $this->iClickOnTheText("Order status");
                }
                if($ids_row_count==51)
                {
                  $this->iClickOnTheText("User information");
                }
                if($ids_row_count==54)
                {
                  $this->iClickOnTheText("Order history");
                }
                 if($ids_row_count==57)
                {
                  $counter=0;
                  $div_with_table=$this->getSession()->getPage()->find("css","#line-item-manager");
                  $this->setClassTable("sticky-enabled","row_id");
                  $tds=$this->getSession()->getPage()->findAll("css",".row_id_1");
                  foreach($tds as $td)
                  {
                   if($td->getText()!="") {array_push($dev_array,$td->getText());$counter++;}
                   
                  }
               
                } 


            } 
            $session->visit("http://lushukos.prod.acquia-sites.com/user/logout");
            sleep(1);
            /*
             for($i=0;$i<sizeof($beta_array);$i++) 
              {
                
                  if($beta_array[$i] !== $dev_array[$i])
                  {
                    echo "The problem is on ".$beta_site." and ".$dev_site."\n\n";
                    switch ($i) 
                    {
                      case 0:
                        echo "Different Title - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 1:
                        echo "Different Category - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 2:
                        echo "Different Image - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 3:
                        echo "Different Entity Title - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 4:
                        echo "Different Entity SKU - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 5:
                        echo "Different Entity Price - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 6:
                        echo "Different Entity Status - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 7:
                        echo "Different Strapline - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 8:
                        echo "Different Summary - \n Beta site =".$beta_array[$i]."\n Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 9:
                        echo "Different Product Discription - \n Beta site =".$beta_array[$i]."\n Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 10:
                        echo "Different Text Format - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 11:      
                        echo "Different Product Type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 12:
                        echo "Different How to use - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 13:
                        echo "Different How to use Format - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 14:
                        echo "Different Excluded from search - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 15:
                        echo "Different Revision - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 16:

                        echo "Different Comments 1 - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 17:
                        echo "Different Comments 2 - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 18:
                        echo "Different Created Date - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 19:
                        echo "Different Promote - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;     
                      case 20:
                        echo "Different Sticky - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 21:
                        echo "Different Number of Media - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 22:
                        echo "Different Feel - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 23:
                        echo "Different Form - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;    
                      case 24:
                        echo "Different Colour - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 25:
                        echo "Different Packaging type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 26:
                        echo "Different Scent - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 27:
                        echo "Different Certification type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 28:
                        echo "Different Preservatives free - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 29:
                        echo "Different Preservatives free - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;   
                      case 30:
                        echo "Different Preservatives free - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 31:
                        echo "Different 100% Natural   - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 32:
                        echo "Different 100% Natural   - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 33:
                        echo "Different 100% Natural   - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 34:
                        echo "Different Featured review   - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 35:
                        echo "Different Featured article  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 36:
                        echo "Different Related  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 37:
                        echo "Different Kitchen  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 38:
                        echo "Different Chef  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 39:
                        echo "Different Kitchen status  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 40:
                        echo "Different Kitchen Sticker - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 41:
                        echo "Different Google Title  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 42:
                        echo "Different Google URL  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 43:
                        echo "Different Kitchen Date  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 44:
                        echo "Different Lead time  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 45:
                        echo "Different Currently unavailable  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 46:
                        echo "Different Excluded from discount - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 47:
                        echo "Different Seasonal discount  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                               
                    }
                  }
                
              }
*/            var_dump($beta_array);
              unset($beta_array);
              $beta_array = array();
              unset($dev_array);
              $dev_array = array();
          }
          #Logout
              
           fclose($file_ids);
            
      fclose($file);

}


/**
     * Save data from provided CSS selector (LUSH SPECIFIC CASE!!!!)
     *
     * @When /^I compare LUSH dev and beta article csv file for links "([^"]*)" and csv for ids "([^"]*)" from row "([^"]*)"$/
     */
    public function articleCompareLUSH($links_csv,$ids_csv,$row)
    {
        $dev_array=array();
        $beta_array=array();

        $file = fopen($links_csv,"r");
        $processing = 0;
        $counter = 0;
        $limit=1;

        while(! feof($file))
        {
          
          $csv_row = fgetcsv($file);
          $counter++;
          if ($counter < $row+1)
          {
            continue;
          }
          if ($processing == $limit)
          {
            break;
          }
          $processing++;

          $product_type = $csv_row[0];
          $beta_site = $csv_row[1];
          $dev_site = $csv_row[2];

          
              $session = $this->getSession();
              $page = $session->getPage();
              
              $session->visit("http://lush:t-shirt-phone-pen-table@beta.lush.co.uk/user/login");
              $page->fillField("edit-name", "biser");
              $page->fillField("edit-pass", "123456Pm1");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($beta_site);


              $file_ids = fopen($ids_csv,"r");
              $ids_row_count=0;
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                
                    array_push($beta_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'text_area':
                
                    array_push($beta_array,$this->paragraphTrim($this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getText')));
                    
                    break;

                  case 'select':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                
                    break;  

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;

                  case 'select_field':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($beta_array,0);
                    }
                    else 
                    {
                       array_push($beta_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;

                  case 'review':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;  
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($beta_array,1);
                       }
                        else 
                       {
                          array_push($beta_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($beta_array,0);
                    }
                    break;
                  }


                 if($ids_row_count==3)
                 {
                  $this->iClickOnTheElement("#wysiwyg-toggle-edit-body-und-0-value");                  
                 }                           
                 if($ids_row_count==8)
                 {
                  $this->iClickOnTheText("Comment settings");
                 }
                 if($ids_row_count==8)
                 {
                  $this->iClickOnTheText("Meta tags");
                 }
                 if($ids_row_count==16)
                {
                  $this->iClickOnTheText("Authoring information");
                }
                if($ids_row_count==19)
                {
                  $this->iClickOnTheText("Publishing options");
                }
                if($ids_row_count==23)
                {
                  $this->iClickOnTheText("Authorship");
                 
                }
                 if($ids_row_count==28)
                 {
                   
                  $this->iClickOnTheText("Media");
                  $this->setIdImgFields("field_group-node_article_form_group_media node_article_form_group_media","migration_img_id");
                  sleep(2);
                  array_push($beta_array,$this->CountAppOf("#migration_img_id"));
                 }

                if($ids_row_count==29)
                {  
                  $this->iClickOnTheText("Classification");
                }

                if($ids_row_count==34)
                {
                  $this->iClickOnTheText("Relationships");
                }
                if($ids_row_count==35)
                {
                   $finded_input=$this->getSession()->getPage()->find('css',"#edit-field-related-product-und-0-target-id");               
                  array_push($beta_array,preg_replace('/\d+/', '', $finded_input->getValue()));
                }
                if($ids_row_count==36)
                {
                  $this->iClickOnTheText("Social media");
                }
                if($ids_row_count==39)
                {
                  $this->iClickOnTheText("Quiz");
                }
               


            } 

                 fclose($file_ids);

           #Logout
              $session->visit("http://beta.lush.co.uk/user/logout");
              sleep(1);
           #STEP 2 LUSH WEBSITE DEV
              $session->visit("http://lush:zom-dars-u@content.lush.com/user/");
              $page->fillField("edit-name", "lush_admin");
              $page->fillField("edit-pass", "tFu-e8E-n6R-frm");
              $page->pressButton("edit-submit");
              $this->getSession()->visit($dev_site);

              $file_ids = fopen("files/ids_article_dev.csv","r");
               $ids_row_count=0;
              while(! feof($file_ids))
              {
                
                #Read by rows the csv with type and id
                $ids_csv_row = fgetcsv($file_ids);
                $ids_row_count++;
                $field_type=$ids_csv_row[0];
                $field_id=$ids_csv_row[1];

                switch ($field_type) {
                  case 'input_text':
                    
                    array_push($dev_array, $this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getValue'));
                    
                    break;

                  case 'text_area':
                
                    array_push($dev_array, $this->paragraphTrim($this->checkFindEmpty($this->getSession()->getPage()->find('css',$field_id), 'getText')));
                    
                    break;

                  case 'select':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                
                    break;  

                  case 'image_field':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,$element->getText());
                    }
                    break;

                  case 'dropdown':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;

                  case 'select_field':
                    $element=$this->getSession()->getPage()->find('css', $field_id." option[selected='selected']");
                    if($element == null)
                    {
                       array_push($dev_array,0);
                    }
                    else 
                    {
                       array_push($dev_array,($element->getText()));
                    }
                    break;
                  
                  case 'checkbox':
                    
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;
                  

                  case 'review':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;  
                  
                  case 'radio_button':
                    $element=$this->getSession()->getPage()->find('css',$field_id);
                    if(isset($element))
                    {
                        if($element->getAttribute('checked'))
                       {
                           array_push($dev_array,1);
                       }
                        else 
                       {
                          array_push($dev_array,0);
                       }
                    }
                    else 
                    { 
                      array_push($dev_array,0);
                    }
                    break;

                  }

               
              
                 if($ids_row_count==3)
                 {
                  $this->iClickOnTheElement("#switch_edit-body-und-0-value");                  
                 }                           
                 if($ids_row_count==8)
                 {
                  $this->iClickOnTheText("Comment settings");
                 }
                 if($ids_row_count==8)
                 {
                  $this->iClickOnTheText("Meta tags");
                 }
                 if($ids_row_count==16)
                {
                  $this->iClickOnTheText("Authoring information");
                }
                if($ids_row_count==19)
                {
                  $this->iClickOnTheText("Publishing options");
                }
                if($ids_row_count==23)
                {
                  $this->iClickOnTheText("Authorship");
                 
                }
                 if($ids_row_count==28)
                 {
                   
                  $this->iClickOnTheText("Media");
                  $this->setIdImgFields("field_group-edit-group_media edit-group_media","migration_img_id");
                  sleep(2);
                  array_push($dev_array,$this->CountAppOf("#migration_img_id"));
                 }

                if($ids_row_count==29)
                {  
                  $this->iClickOnTheText("Classification");
                }

                if($ids_row_count==34)
                {
                  $this->iClickOnTheText("Relationships");
                }
                if($ids_row_count==35)
                {
                   $finded_input=$this->getSession()->getPage()->find('css',"#edit-field-related-product-und-0-target-id");               
                  array_push($dev_array,preg_replace('/\d+/', '', $finded_input->getValue()));
                }
                if($ids_row_count==36)
                {
                  $this->iClickOnTheText("Social media");
                }
                if($ids_row_count==39)
                {
                  $this->iClickOnTheText("Quiz");
                }


            } 
            $session->visit("http://content.lush.com/user/logout");
            sleep(1);
           
             for($i=0;$i<sizeof($beta_array);$i++) 
              {
                
                  if($beta_array[$i] !== $dev_array[$i])
                  {
                    echo "The problem is on ".$beta_site." and ".$dev_site."\n\n";
                    switch ($i) 
                    {
                      case 0:
                        echo "Different Title - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 1:
                        echo "Different Article type - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 2:
                        echo "Different Summary - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 3:
                        echo "Different Body - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 4:
                        echo "Different Strapline - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 5:
                        echo "Different Quote - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 6:
                        echo "Different Comments 1 - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 7:
                        echo "Different Comments 2 - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 8:
                        echo "Different Meta title - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 9:
                        echo "Different Meta description - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 10:
                        echo "Different Meta abstract - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 11:      
                        echo "Different Meta keywords - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 12:
                        echo "Different Created by  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 13:
                        echo "Different Created Date - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 14:
                        echo "Different Status - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 15:
                        echo "Different Promote  - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 16:
                        echo "Different Sticky - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 17:
                        echo "Different Author - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 18:
                        echo "Different Photographer - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 19:
                        echo "Different Videographer - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;     
                      case 20:
                        echo "Different Thanks to - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 21:
                        echo "Different Number of media - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 22:
                        echo "Different Category - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 23:
                        echo "Different Article theme - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;    
                      case 24:
                        echo "Different Feel - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;
                      case 25:
                        echo "Different Tags - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 26:
                        echo "Different Related product - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 27:
                        echo "Different Social media - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;  
                      case 28:
                        echo "Different Embedded code - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break; 
                      case 29:
                        echo "Different Quiz - Beta site =".$beta_array[$i]." Dev site =".$dev_array[$i]."\n\n";
                        break;   
                    
                    }
                  }
                
              }

              unset($beta_array);
              $beta_array = array();
              unset($dev_array);
              $dev_array = array();
          }
          #Logout
              
           fclose($file_ids);
            
      fclose($file);

}


/** Go to specific LUSH page using parameters
 * @Given /^I go to LCW "([^"]*)"$/
 */
public function iGoToLCW($lcw)
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params[3].$lcw;
        $this->getSession()->visit($this->locatePath($urlPath));

    }
/** Go to specific LUSH page using parameters
 * @Given /^I go to CR "([^"]*)"$/
 */
public function iGoToCR($cr)
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params[7].$cr;
        $this->getSession()->visit($this->locatePath($urlPath));

    }
/** Go to specific LUSH page using parameters
 * @Given /^I go to OS "([^"]*)"$/
 */
public function iGoToOS($os)
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params[11].$os;
        $this->getSession()->visit($this->locatePath($urlPath));

    }
/** Go to specific LUSH page using parameters
 * @Given /^I go to SML "([^"]*)"$/
 */
public function iGoToSML($sml)
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params[15].$sml;
        $this->getSession()->visit($this->locatePath($urlPath));

    }
/** Go to specific LUSH page using parameters
 * @Given /^I go to SSO "([^"]*)"$/
 */
public function iGoToSSO($sso)
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params[19].$sso;
        $this->getSession()->visit($this->locatePath($urlPath));

    }
/** Go to specific LUSH page using parameters
 * @Given /^I go to CC "([^"]*)"$/
 */
public function iGoToCC($cc)
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params[23].$cc;
        $this->getSession()->visit($this->locatePath($urlPath));

    }
/** Go to specific LUSH page using parameters
 * @Given /^I go to OMS "([^"]*)"$/
 */
public function iGoToOMS($oms)
    {

        $session = $this->getSession();
        $page = $session->getPage();
        $urlPath = $this->params[27].$oms;
        $this->getSession()->visit($this->locatePath($urlPath));

    }

   /** Log in Lush profiles with provided username and password.
     * @Given /^I log in at "([^"]*)"$/
     */
    public function iLogInatLUSH($profile)
    {

        $session = $this->getSession();
        $page = $session->getPage();

        $site = $profile;
        switch($profile)
        { 

          case "LCW" : 
        $usern = $this->params[0];
        $passw = $this->params[1];
        break;

        case "CR" : 
        $usern = $this->params[4];
        $passw = $this->params[5];
        break;

        case "OS" : 
        $usern = $this->params[8];
        $passw = $this->params[9];
        break;

        case "SML" : 
        $usern = $this->params[12];
        $passw = $this->params[13];
        break;

        case "SSO" : 
        $usern = $this->params[16];
        $passw = $this->params[17];
        break;

        case "CC" : 
        $usern = $this->params[20];
        $passw = $this->params[21];
        break;

        case "OMS" : 
        $usern = $this->params[24];
        $passw = $this->params[25];
        break;

        }
        if($profile == "OMS")
        {
          $page->fillField("_username", $usern);
          $page->fillField("_password", $passw);
          $page->pressButton("Sign in");
        }
        else
        {
          $page->fillField("edit-name", $usern);
          $page->fillField("edit-pass", $passw);
          $page->pressButton("edit-submit");
        }
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



/** Click on the View button on saved data row.
 * @Given /^I click on the View of saved data row$/
 */
public function iClickOnViewSavedData()
{
    global $saved_data;
    $num=preg_replace("UK-LW-","");
    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('xpath',$session->getSelectorsHandler()->selectorToXpath('xpath', '//*[contains(text(),"'.$saved_data.'")]'));
    if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" on the page ', $saved_data));
        }
      
        $element->click();
}

public $sku;
/**
* @Given I enter SKU in :hacked field
*/
public function iEnterSkuInField($hacked)
  {
    global $sku;
    $num=date("His");
    $sku=$num;
    $session = $this->getSession();
    $element = $session->getPage()->find('css',$hacked);
    if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" on the page ', $hacked));
        }
        $element->setValue($num);
  }

  
/**
* @Given I enter generated SKU in :hacked field
*/
public function iEnterGeneratedSkuInField($hacked)
  {
    global $sku;
    $session = $this->getSession();
    $element = $session->getPage()->find('css',$hacked);
    if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" on the page ', $hacked));
        }
        $element->setValue($sku);
  }

/**
* Save order number
*
* @When /^I save order number "([^"]*)"$/
*/
public function iSaveOrderNumber($method)
{
    global $saved_data;

  if($method=="Card")
   {
  $session = $this->getSession(); // get the mink session
  $element = $session->getPage()->find('css',"#edit-checkout-completion-message");
  $re = "/UK-LW-\d+/";
  $order_arr=array();
  preg_match($re,$element->getText(),$order_arr);
  $saved_data=$order_arr[0];
   }

  else if ($method=="Phone")
   {
    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css',"#edit-lush-payment-checkout-complete-pay-by-phone");
    $re = "/UK-LW-\d+/";
    $order_arr=array();
    preg_match($re,$element->getText(),$order_arr);
    $saved_data=$order_arr[0];
   }

  else if($method=="CC")
   {
  $session = $this->getSession(); // get the mink session
  $element = $session->getPage()->find('css',"#edit-order-number");
  $re = "/UK-OS-\d+/";
  $order_arr=array();
  preg_match($re,$element->getText(),$order_arr);
  $saved_data=$order_arr[0];
   }
 
}
  /**
  * debug function
  *
  * @When /^I debug page$/
  */
  public function DebugFunction()
  {
   $session=$this->getSession();
        $page=$session->getPage();
     #search partial
        $escapedValue = $session->getSelectorsHandler()->xpathLiteral('edit-payments-list-transactions');
        
        $elements=$page->findAll("named_partial",array('select',$escapedValue));
        if(isset($elements))
        {
          var_dump("YES");
        var_dump($elements);
      }
      else 
      {
        var_dump("No");
      }

    
  }

    /**
     * Save the node_ID
     *
     * @When /^I save node id$/
     */
    public function iSaveNodeId()
    {
        global $node_ID;
        $tested_url=$this->getSession()->getCurrentUrl();
        $pattern_node_ID = "(\\d+)";
        preg_match($pattern_node_ID, $tested_url, $node_ID_array);
        $node_ID = $node_ID_array[0];
    }

     /**
     * Save the node_ID for entity
     *
     * @When /^I visit page with nodeID "([^"]*)" environment$/
     */
    public function iGoToURLNodeID($environment)
    {
        global $node_ID;
        $session=$this->getSession();
        switch($environment)
        { 

          case "LCW" : 
          $urlPath = $this->params[3];
          break;

          case "CR" : 
          $urlPath = $this->params[7];
          break;

          case "OS" : 
          $urlPath = $this->params[11];
          break;
        }
        $visit_url=$urlPath."admin/commerce/products/".$node_ID;
        $session->visit($visit_url);
    }
    
      /**
     * Get text td.
     * HARDCODED FOR LUSH reasons
     *
     * @When /^I check that the reservation is "([^"]*)"$/
     */
    public function iCheckReservation($number)
    {
        
        $session=$this->getSession();
        $page=$session->getPage();
        $element=$page->find('xpath',"//div[3]/div[2]/div[2]/div[2]/table/tbody/tr/td[2]");
        if(isset($element))
        {
        $td=$element->getText();
        if($number==$td) return true;
        else throw new Exception(sprintf('The reservation "%s" is not equal to "%s"',$td,$number));
      }
      else throw new Exception(sprintf('Element not found!'));
    }

    /**
     * Get text td.
     * HARDCODED FOR LUSH reasons
     *
     * @When /^I check that the status is "([^"]*)"$/
     */
    public function iCheckStatus($status)
    {
        
        $session=$this->getSession();
        $page=$session->getPage();
        $element=$page->find('xpath',"//div[4]/div[2]/div[2]/div/div/div/div/table/tbody/tr[2]/td[5]");
        if(isset($element))
        {
        $td=$element->getText();
        if(strpos($td,$status)) return true;
        else throw new Exception(sprintf('The reservation "%s" is not equal to "%s"',$td,$status));
      }
      else throw new Exception(sprintf('Element not found!'));
    }

     /**
     * Fill in OMS field with Order-NUM
     * HARDCODED FOR LUSH reasons
     *
     * @When /^I fill in search field with order number$/
     */
    public function omsOrderNumField()
    {
        global $saved_data;
        $session=$this->getSession();
        $page=$session->getPage();
        $pattern_order_num = "(\\d+)";
        preg_match($pattern_order_num, $saved_data, $order_num_arr);
        $order_num=$order_num_arr[0];
        $page->fillField("s","W".$order_num);
        $function = <<<JS
      (function(){
      var elements = document.getElementsByClassName("glyphicon-search");
      elements[0].id="search_button";
      })()
JS;
       $this->getSession()->executeScript($function);
       $search_button=$page->find("css","#search_button");
       $search_button->click();
    }


      /**
     * Click Manage Stock for certain product in OMS
     * HARDCODED FOR LUSH reasons
     *
     * @When /^I click on manage stock$/
     */
    public function omsSetManageStockId()
    {

        $function = <<<JS
      (function(){
      var elements = document.getElementsByTagName("tr");

        for (var i = 0; i < elements.length; i++) 
        {
          if (elements[i].style.display != "none" && elements[i].className=="")
          {
            elements[i].className="manage_stock_class";
          }
        }
        var tds = document.getElementsByClassName("manage_stock_class")
        var links = tds[1].getElementsByTagName("a");
        for(var i=0; i < tds.length; i++)
        {
          if(links[i].text=="Manage Stock")
          {
            links[i].id="manage_stock_id";
          }
        }
      })()
JS;
       $this->getSession()->executeScript($function);  
       $session=$this->getSession();
       $page=$session->getPage();
       $link=$page->find("css","#manage_stock_id");
       $link->click();
    }



    /**
    * Click on input with certain value
    *
    * @When /^I click on the input with value "([^"]*)"$/
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
