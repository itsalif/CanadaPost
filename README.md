Canada Post PHP Class
====================

 Canada Post PHP Class will communicates with Canada Post Server and gets the shipping estimate. Its basic 
 and can be extended. 


How to Use
----------
 
  As per Canada Post's specification, make sure Port 30000 is opened.

  At first include the php class file.

  include 'path/to/class.canadapost.php';
  

  $cPost = new CanadaPost();

  Set your manufacturer CPCID, postal code by calling the function setManufacturer 
  
<pre>  

  $cPost->setManufacturer( array (
      'merchantCPCID' => CPC_DEMO_XML, // use your merchantCPCID
      'fromPostalCode' => YOUR_POSTAL_CODE, // use your postal code from where the item will be shipped
      'turnAroundTime' => 24,
      'itemsPrice' => 14 // put the total cost of item to be shipped.
    )
  ); 

</pre>

  Then set the Customer address in the format shown below, again in associative array format.
  Note: city and provOrState are optional. Only Postal Code and country is required. 

<pre>
   
  $cPost->setCustomer( array (
     'city' => 'Brampton', [CUSTOMER_CITY]
     'provOrState' => 'Ontario', [CUSTOMER_STATE_PROVINCE]
     'country' => CA, [2 Digit Code, see Canada Post Specs for more Info]
     'postalCode' => 'L1JK9' [CUSTOMER_POSTAL_OR_ZIP_CODE]
   )
  );

</pre>

  Then, set the total price of shipping

<pre>
  
  $cPost->setPrice(15);

</pre>
  
  Then, add the products needed to be shipped (add as many as you want), in the format shown below:
  
<pre>
  
  $cPost->addProduct(  array (
    'quantity' => 1,
    'weight'=> 2,
    'length' => 3,
    'width' => 1,
    'height' => 8,
    'description' => 'some Description about Product'
   )
  );

</pre>
  
  Then, invoke the method below (returns XML format of details from Canada Post Server):

<pre>  
  $responseXML = $cPost->getRates();  
</pre>  
  
  If you wish to get the response in associative array, use the following:

<pre>
  $responseArray = $cPost->getRates('array');
</pre>  
  
  If any error occurs, the above method should return false. So, you can check it like below:

<pre>  
  $rXML = $cPost->getRates(); 
  if( $rXML === false ) {
    echo $cPost->getErrorMessage();
  }
</pre>  
  
  For a sample demo, please check out demo.php


Change Log 
----------

* Version 1.1 (Dec 14, 2010)
  
  License Changed to MIT License. Updated to Github SVN. No changes in code.   
  
* Version 1 (July 2008)
  
  Creation of PHP Class. 

  
Online Documentation / Demo
---------------------------

* Online Demo / Documentation: <http://www.itsalif.info/content/php-shipping-modules-canada-post>
  
       
License & Policy
---------------------------

Copyright (c) 2008 Abdullah Rubiyath <http://www.itsalif.info>. 
The script has been released under MIT License.
