<?php
error_reporting(0);

//define ID info for access to the API
define("Access_Key_ID", "ACCESS_KEY_ID_GOES_HERE");
define("Associate_tag", "dev_name_goes_here");
//HTML code for the form 
echo "
<!DOCTYPE html>
<html lang='en' class='text-center'>
<title>ASIN Form</title>
<link rel='stylesheet' type='text/css' href='//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css' media='screen'/>
<h1>ASIN:</h1>
<form action='http://localhost/phptestNewest.php' method='post' class='form-inline'>
<div class='form-group'>
 <p class = 'lead'><input type='text' name='asin' class='form-control' placeholder='Type ASIN Here' autofocus='autofocus'/></p>
 </div>
 <p><input type='submit' class='btn btn-secondary btn-default' value = 'Submit' /></p>
</form>
<html>";
 

function ItemLookup(){
	//recieve the data entered into the HTML form
$ASIN =  (isset($_POST['asin']) ? $_POST['asin'] : null);
//$ASIN =  htmlspecialchars($_POST['asin']);
//$ASIN = "B007PTCFFW"; //ASIN for product
$current_date = gmDate("Y-m-d\TH:i:s\Z"); //enter current date (used for timestamp) 
//Set the values for some of the parameters
$Operation = "ItemLookup"; //operation used for the request
$Version = "2013-08-01";
//User interface provides values
//for $SearchIndex and $Keywords
//Define the request parameters for the URL
$request=
     "http://webservices.amazon.com/onca/xml"
   . "?Service=AWSECommerceService"
   . "&AssociateTag=" . Associate_tag
   . "&AWSAccessKeyId=" . Access_Key_ID
   . "&Operation=" . $Operation
   . "&Version=" . $Version
   . "&ResponseGroup=" . "ItemAttributes"
   . "&Timestamp=" . $current_date
   . "&ItemId=". $ASIN;
//make a new variable called URL, which is the first part of the 
//request using the variable $request. // the signature and current timestamp still need
//to be added
$url = $request;
$secret_key = "SECERET_KEY_GOES_HERE"; //Amazon secret key for ID purposes, insert your AWS secret key here

// Decode anything already encoded in the url
//$url = urldecode($request);

    // Parse the URL into $urlparts
    $urlparts = parse_url($url);

    // Build $params with each name and value pair
    // the & is an indicator that there is a new part in the URL
    foreach (split('&', $urlparts['query']) as $part) {
        if (strpos($part, '=')) {
        	//find the equals signs in the link
        	//and grab its value and the name listed before it.
            list($name, $value) = split('=', $part, 2);
        } else {
            $name = $part;
            $value = '';
        }
        $params[$name] = $value;
    }

    //if there wasnt a value for $params for timestamp, generate a new one
    //and insert it into the request.
    if (empty($params['Timestamp'])) {
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
    }

    // Sort the array by key
    ksort($params);

    // Build the canonical query string
    $canonical       = '';
    foreach ($params as $key => $val) {
        $canonical  .= "$key=".rawurlencode(utf8_encode($val))."&";
    }
    // Remove the trailing ampersand
    $canonical       = preg_replace("/&$/", '', $canonical);

    // Some common replacements and ones that Amazon specifically mentions
    $canonical       = str_replace(array(' ', '+', ',', ';'), array('%20', '%20', urlencode(','), urlencode(':')), $canonical);

    // Build the sign
    $string_to_sign             = "GET\n{$urlparts['host']}\n{$urlparts['path']}\n$canonical";
    // Calculate our actual signature and base64 encode it
    $signature            = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_key, true));

    // Finally re-build the URL with the proper string and include the Signature
    $url = "{$urlparts['scheme']}://{$urlparts['host']}{$urlparts['path']}?$canonical&Signature=".rawurlencode($signature);

//print All the info:

$xml = simplexml_load_file($url); 
//make sure that there was an ASIN entered and that it was valid
if($ASIN != null && $xml->Items->Item->ItemAttributes->Title != null){
//print title:
echo "<div class = 'text-left'>"; 
echo "<strong>Title: </strong>";
echo $xml->Items->Item->ItemAttributes->Title."<br>";
//Print the word features
echo "<br>"."<strong>Features: </strong>"."<br>";

for($number = 0; $number < 7; $number= $number +1){
	//there were many features included in the product info,
	//so instead of listing each one I made a for loop
echo $xml->Items->Item->ItemAttributes->Feature[$number]."<br>";

}
//print ASIN
echo "<br>"."<strong>ASIN: </strong>";
echo $xml->Items->Item->ASIN."<br>";
//print Manufacturer
echo "<br>". "<strong>Manufacturer: </strong>";
echo $xml->Items->Item->ItemAttributes->Manufacturer."<br>";
//print Part number
echo "<br>". "<strong>Part Number: </strong>";
echo $xml->Items->Item->ItemAttributes->PartNumber."<br>";
//Print UPC number
echo "<br>". "<strong>UPC: </strong>";
echo $xml->Items->Item->ItemAttributes->UPC."<br>";
//print price
echo "<br>". "<strong>Formatted Price: </strong>";
echo $xml->Items->Item->ItemAttributes->ListPrice->FormattedPrice."<br>";
//print category
echo "<br>". "<strong>Category: </strong>";
echo $xml->Items->Item->ItemAttributes->Binding."<br>";
//Print Item Demensions
	echo "<br>". "Weight: ";
	echo $xml->Items->Item->ItemAttributes->ItemDimensions->Weight."<br>";

	echo "<br>". "Height: ";
	echo $xml->Items->Item->ItemAttributes->ItemDimensions->Height."<br>";

	echo "<br>". "Length: ";
	echo $xml->Items->Item->ItemAttributes->ItemDimensions->Length."<br>";

	echo "<br>". "Width: ";
	echo $xml->Items->Item->ItemAttributes->ItemDimensions->Width."<br>";
//print URL to Amazon Store page
//echo "<br>". "URL for Product: "."<br>";
//echo $xml->Items->Item->DetailPageURL."<br>";
//make a clickable product link
echo "<br>"."<a href=".$xml->Items->Item->DetailPageURL.">Click Here For Product Link</a>";
echo "</div>"; 
}
// if an ASIN was entered but it wasnt valid, tell the user
else if($ASIN != null&& $xml->Items->Item->ItemAttributes->Title == null){
	echo "<p class = 'help-block'>Please Submit Valid ASIN</p>";

}
}
//execute the above function
ItemLookup();

?>