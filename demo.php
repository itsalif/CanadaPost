  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Estimate Shipping Form</title>
<style type="text/css" media="all">

div.shippingRatesDiv, div.shippingRatesDivHeader {
	font: normal 12px Verdana, Arial;
	padding: 2px;
	margin-top: 5px;
	margin-left: 10px;
	display: block;
}

div.shippingRatesDiv div, div.shippingRatesDivHeader div {
	float: left;
	width: 125px;
}
	
div.shippingRatesDivHeader {
	margin-bottom: 5px;
}
	

div.clear {
	clear: both;
}

div.shippingForm {
	font: normal 12px Verdana, "Trebuchet MS", Arial;
	color: #000;
	padding: 2px 2px 2px 10px;; 
}

div.shippingForm span {
	padding: 2px;
	float: left;
	width: 150px;
	display: block;
}

div.shippingForm div {
	clear: both;
	padding: 2px 0px;
}

div.estimateTitle {
	color: #000;
	font: bold 12px Verdana, Arial;
	padding: 2px 2px 2px 5px;
}


</style>
</head>

<body>

<div>

	<div class="estimateTitle">Default my address is L6V4K9 and I have used CPC_DEMO_XML (default Canada Post key): </div>	
<div class="shippingForm">
	<form name="checkShipping" action="demo.php?products_id=<?php echo $_GET['products_id']; ?>" method="post">
		<div><span>Zip / Postal Code:</span> 	<input type="text" name="postal" value="<?php echo $_POST['postal']; ?>" />	</div>
		<div><span>Country:</span> 	<select name="country">
			<option value="CA">Canada</option>
			<option value="US">U.S.A</option>			
		</select> </div>
		<div><span>Quantity:</span> 	<input type="text" 	name="quantity" value="1" /></div>
<br /><br />Enter your Product Info below: <br /><br />
		Price: <input type="text" name="price" value="<?php echo $product_check['products_price']; ?>" /> <br />
		
<br />		
Weight: <input type="text" name="weight" value="<?php echo $product_check['products_weight']; ?>" />
<br />		
Length: <input type="text" name="length" value="<?php echo $product_check['products_length']; ?>" />
<br />		
Width: <input type="text" name="width" value="<?php echo $product_check['products_width']; ?>" />		 <br />
Height:	<input type="text" name="height" value="<?php echo $product_check['products_height']; ?>" />				
		<input type="hidden" name="ttime" value="<?php echo time(); ?>" />
		<input type="submit" name="submit" value="Estimate" /> &nbsp; <input type="button" name="close" onclick="javascript:window.close();" value="Close Window" />
	</form>
</div>

<?php if($_SERVER['REQUEST_METHOD'] == "POST" )	 {
	
	if( isset($_POST['ttime']) ) {
		require_once("class.canadapost.php");
		$cPost = new CanadaPost();
		$cPost->setPrice($_POST['price']);
		$cPost->setCustomer( array( 
				'city' => $_POST['city'],
				'provOrState' => $_POST['prov'],
				'country' => $_POST['country'],
				'postalCode' => $_POST['postal']
			 ) 
		);
		 
		$cPost->addProduct ( array (
		   		'quantity' => $_POST['quantity'],
				'weight' => $_POST['weight'],
				'length' => $_POST['length'],
				'width' => $_POST['width'],
				'height' => $_POST['height'],
				'description' => ' '
			)
		); 
		
		$assoc_Array = $cPost->getRates('array');
		if( $assoc_Array === false ) {
			echo 'Error: ' . $cPost->getErrorMessage();
		} else {
//		echo "<pre>";
//		var_dump( $assoc_Array );
//		echo "</pre>";
	

?>
	<div style="border-bottom: solid 1px #ccc; height: 5px; margin-bottom: 5px;"></div>
	<div class="shippingRatesDivHeader">
		<div><strong> Shipping Type </strong></div>
		<div><strong> Rate ($) </strong></div>
		<div><strong> Est. Shipping Date </strong></div>
		<div><strong> Est. Delivery Date </strong></div>
		<div class="clear"></div>
	</div>
	
	<div class="shippingRatesDiv">	
<?php	

		foreach( $assoc_Array['product'] as $eachProduct )	{
			echo "<div>".$eachProduct['name']."</div>";
			echo "<div>$".$eachProduct['rate']."</div>";
			echo "<div>".$eachProduct['shippingDate']."</div>";
			echo "<div>".$eachProduct['deliveryDate']."</div>";
			echo "<div class=\"clear\"></div>";															
			}
?>
	</div>
<?php	
//	echo "<br /><br />Sending XML Below: <pre>".htmlentities($cPost->getSendXML())."</pre>";
//		echo "ResponseXML Below: <pre>".htmlentities($cPost->getResponseXML())."</pre>";	
		}

	
}

} 
?>

</div> 
	
</body>
</html>
