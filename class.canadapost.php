<?php

/***
 * This class will allow you to fetch information from Canada Post Server and show the information to you. 
 * Its a very basic and can be extended. 
 * 
 * @author Abdullah Rubiyath (c) July 2008. 
 * @version 1.0
 * @license MIT 
 * 
 * Usage: 
 * 
 * At Constructor Merchant Info & Customer Info needs to be passed in associative array in this format:
 * 
 * $merchantInfo = array (
 *    'merchantCPCID' => CPC_DEMO_XML,
 *    'fromPostalCode' => YOUR_POSTAL_CODE,
 *    'turnAroundTime' => 24,
 *    'itemsPrice' => 14
 *  );
 *  
 * $customerInfo = array (
 *    'city' => 'Brampton', [CUSTOMER_CITY]
 *    'provOrState' => 'Ontario', [CUSTOMER_STATE_PROVINCE]
 *    'country' => CA,   [2 Digit Code, see Canada Post Specs for more Info]
 *    'postalCode' => 'L1JK9' [CUSTOMER_POSTAL_OR_ZIP_CODE]
 * );
 *  
 * Product information needs to be passed in this following format in Associate Array:
 * 
 * $product_info = array (
 *   'quantity' => 1,
 *   'weight'=> 2 (kg),
 *   'length' => 3 (cm),
 *   'width' => 1 (cm),
 *   'height' => 8 (cm),
 *   'description' => 'some Description about Product'
 * );
 * 
 * To get a better understanding of XML Please see CanadaPost's DTD:
 * http://sellonline.canadapost.ca/DevelopersResources/protocolV3/eParcel.dtd 
 * 
 * Sample XML POST AND RESPONSE FROM Canada Post can be viewed here:
 * http://sellonline.canadapost.ca/DevelopersResources/protocolV3/HTTPInterface.html
 */

 
 
class CanadaPost {
	
	protected $sendXML;
	protected $responseXML;
	protected $merchantInfo;
	protected $customerInfo;
	protected $productInfo;
	protected $errorMessage;
	
	/** the constructor where all values are initialized **/
	public function __construct() {
		$this->merchatInfo = array ( 
 			'merchantCPCID' => 'CPC_DEMO_XML',
		    'fromPostalCode' => 'L6V4K9',
		    'turnAroundTime' => '24',
		    'itemsPrice' => '0'		
		);
		$this->productInfo = array();
		$this->errorMessage = array (
			'code' => 0,
			'message' => 'success'
		);
	}


	/**
	 * This Method sets the Manufacturer information
	 * @return none
	 * @param array $mInfo Array containing Manufacturer Info in Associative format
	 */	
	public function setManufacturer($mInfo) {
		$this->merchantInfo = $mInfo;
	}
	

	/**
	 * This Method sets the Item Price
	 * @return none
	 * @param float $itemPrice Float containing the total price of items to be shipped
	 */
	public function setPrice($itemPrice) {
		$this->merchantInfo['itemsPrice'] = $itemPrice;
	}	
	
	/**
	 * This Method sets the Customer Info
	 * @return none
	 * @param array $cInfo Array containing the Customer's Info in Associative Format 
	 */
	public function setCustomer($cInfo) {
		$this->customerInfo = $cInfo;
	}
	
	/**
	 * This Method allows you to Add items to be shipped
	 * @return none
	 * @param array $pInfo Array containing product Info in 
	 */
	public function addItem($pInfo) {
		$this->productInfo[] = $pInfo;
	}
	
	/**
	 * This Method allows you to add Product
	 * @return none
	 * @param array $pInfo Array containing product Info in 
	 */
	public function addProduct($pInfo) {
		$this->productInfo[] = $pInfo;
	}	
	
	/**
	 * This Method Returns Data by fetching from Canada Post's Server. Depending on parameter passed, it
	 * returns either Array in Associative format or an XML String. On failure it returns false.
	 * 
	 * @return string|boolean|array Returns false for error or either XML String or in Associative array.
	 * @param string $return String  'xml' or 'array' 
	 */ 	
	public function getRates($returnString = 'xml') {
		
		$pData = $this->prepareXML();
//		$this->sendXML = $pData;
		$context_options = array (
   	    	'http' => array (
   		   	    'method' => 'POST',
    	   	    'header'=> "Host: sellonline.canadapost.ca\n"
						 . "Content-type: application/x-www-form-urlencoded\r\n"
			             . "Content-Length: " . strlen($pData) . "\r\n"
    	       )
    	);
	
		$context = stream_context_create($context_options);	
		// socket Connection to CanadaPost Server with proper context
		$socket = @stream_socket_client('sellonline.canadapost.ca:30000', $errno,
				 $errstr, 15, STREAM_CLIENT_CONNECT, $context);
		
		if( !$socket ) {
			$this->errorMessage['code'] = $errno;
			$this->errorMessage['message'] = $errstr;
			return false;
		}
			 
		fwrite($socket, $pData);
		$responseXML = "";
		while (!feof($socket) ) {
			$responseXML .= fgets($socket,255);
	   }	
	   fclose($socket);			
	   
	   if( $returnString == 'xml' ) {
			return $responseXML;
	   } else if( $returnString == 'array') {
	   	
			// make an assoc array by using xpath on Dom and the basic elements in an assoc array
	   		$rArray = array();	
   			$rDoc 	= new DomDocument();
   			$rDoc->loadXML($responseXML);
   			$xpath 	= new DomXPath($rDoc);
			
   			$statusCode = $xpath->query('//statusCode');
			
			// check for Error, if there's error then return false
			if( $statusCode->item(0)->nodeValue != "1" ) {
				$this->errorMessage['code'] 	= $statusCode->item(0)->nodeValue;
	   			$statusMessage = $xpath->query('//statusMessage');
				$this->errorMessage['message'] 	= $statusMessage->item(0)->nodeValue;
//				var_dump ( $responseXML );
				return false;
			}	
			
   			$rates 	= $xpath->query('/eparcel/ratesAndServicesResponse/product');
     		foreach($rates as $eachRate) { 
  	 			$tempArray = array();
  	 			foreach($eachRate->childNodes as $eachChild) {
   					if( $eachChild->tagName != "" )  	 		
	  			 		$tempArray[$eachChild->tagName] = $eachChild->nodeValue;
  	 			}
				$rArray['product'][] = $tempArray;
   			}
   
   			$packingInfo = $xpath->query('/eparcel/ratesAndServicesResponse/shippingOptions');
   			foreach($packingInfo as $eachPack )	 {
   				$tempArray = array();
   				foreach($eachPack->childNodes as $eachChild) {
   					if( $eachChild->tagName != "" )
		   				$tempArray[$eachChild->tagName] = $eachChild->nodeValue;
   				}
				$rArray['shippingOptions'] = $tempArray;
   			}     
   
			return $rArray;
	   }
	}
	
	
	/**
	 * This Method prepares the XML to be send to Canada Posts's Server.
	 * @return none
	 */
	public function prepareXML() {
		
		$sendXML 	= new DomDocument("1.0");
		$eParcel 	= $sendXML->createElement("eparcel");
		$eParcel->appendChild( $sendXML->createElement('language', 'en') );
		$rSRequest 	= $sendXML->createElement("ratesAndServicesRequest");
		
		foreach($this->merchatInfo as $tag=>$value) {
			$rSRequest->appendChild( $sendXML->createElement($tag, $value) );
		}
		
		$lineItems = $sendXML->createElement('lineItems');
		foreach($this->productInfo as $eachProduct ) {
			$eachItems = $sendXML->createElement('item');
			foreach($eachProduct as $eachKey=>$eachValue) {
				$eachItems->appendChild( $sendXML->createElement($eachKey,$eachValue) );	
			}
			$lineItems->appendChild($eachItems);
		}
		$rSRequest->appendChild( $lineItems );
		foreach($this->customerInfo as $tag=>$value) {
			$rSRequest->appendChild( $sendXML->createElement($tag, $value) );
		}
		
		$eParcel->appendChild( $rSRequest );
		$sendXML->appendChild( $eParcel );
		$sendXML->formatOutput = true;
		return $sendXML->saveXML();
	}
	
	/**
	 * 
	 * @return String Returns Error Message if applicable
	 */
	public function getErrorMessage() {
		return $this->errorMessage['message'];
	}
	
	/**
	 * 
	 * @return array of Error Message
	 */
	
	public function getError() {
		return $this->errorMessage;
	}
	
	/*** ***/
	
	public function getSendXML() {
		return $this->sendXML;
	}
	
	public function getResponseXML() {
		return $this->responseXML;
	}
 }
 
 
