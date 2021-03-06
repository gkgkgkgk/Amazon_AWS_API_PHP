<?php
error_reporting(0);

//define ID info for access to the API
define("Access_Key_ID", "id_ACCESS_KEY_GOES_HERE");
define("Associate_tag", "DEV_NAME_GOES_HERE");
// all the valid categories for the Amazon search
$valueIndex = " All Wine Wireless ArtsAndCrafts Miscellaneous Electronics Jewelry MobileApps Photo Shoes KindleStore Automotive Pantry MusicalInstruments DigitalMusic GiftCards FashionBaby FashionGirls GourmetFood HomeGarden MusicTracks UnboxVideo FashionWomen VideoGames FashionMen Kitchen Video Software Beauty Grocery FashionBoys Industrial PetSupplies OfficeProducts Magazines Watches Luggage OutdoorLiving Toys SportingGoods PCHardware Movies Books Collectibles Handmade VHS MP3Downloads Fashion Tools Baby Apparel Marketplace DVD Appliances Music LawnAndGarden WirelessAccessories Blended HealthPersonalCare Classical";
// make each category a seperate variable that belongs to an array (pieces)
$pieces = explode(" ", $valueIndex);
//HTML Form
echo "<h1 style='font-family:Courier; color:Blue; font-size: 30px;'>New Search:</h1>";
echo "<br><form action='http://localhost/itemSearch.php' method='post'>
 <p>Search Index: <select id = 'index' name='index' />";
 // I used a for loop to add every category as an option in the form 
for($number2 = 1; $number2 < 60; $number2 = $number2+1){
echo "<option value = ".$pieces[$number2].'>'.$pieces[$number2].'</option>';
}
echo "</select></p>
 <p>KeyWord: <input type='text' name='word' /></p>
 <p><input type='submit' /></p>
</form>";

//get the search index and keyword from the form
$SearchIndex =  htmlspecialchars($_POST['index']);
$Keyword =  htmlspecialchars($_POST['word']);
function ItemSearch(){
    //get the search index and keyword from the form

$SearchIndex =  htmlspecialchars($_POST['index']);
$Keyword =  htmlspecialchars($_POST['word']);

//$ASIN = "B007PTCFFW"; //ASIN for product
$current_date = gmDate("Y-m-d\TH:i:s\Z"); //enter current date (used for timestamp) 
//Set the values for some of the parameters
$Operation = "ItemSearch"; //operation used for the request
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
   . "&SearchIndex=". $SearchIndex
   . "&Keywords=". $Keyword
   . "&ResponseGroup=" . "ItemAttributes"
   . "&Timestamp=" . $current_date;
   //. "&ItemId=". $ASIN;
//make a new variable called URL, which is the first part of the 
//request using the variable $request. // the signature and current timestamp still need
//to be added
$url = $request;
$secret_key = "SECRET_KEY_GOES_HERE"; //Amazon secret key for ID purposes

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


$xml = simplexml_load_file($url); 

//only show the ASIN and title search results of info was entered
if($SearchIndex!= null){

for($number = 0; $number < 10; $number= $number +1){
    //there were many features included in the product info,
    //so instead of listing each one I made a for loop
echo $xml->Items->Item[$number]->ItemAttributes->Title.": ";
echo $xml->Items->Item[$number]->ASIN."<br>";
error_reporting(0);
}
}
}
//execute the above function
ItemSearch();
// if a search index was enetered, bring up the option to eneter an ASIN code
if($SearchIndex!= null){
echo "<form action='http://localhost/itemSearch.php' method='post'>
 <p>ASIN: <input type='text' name='asin' /></p>
 <p><input type='submit' /></p>
</form>";}
//get the ASIN codes from the HTML form
$ASIN =  htmlspecialchars($_POST['asin']);
function ItemLookup(){
$ASIN =  htmlspecialchars($_POST['asin']);

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
$secret_key = "u6mPwUtlzERxqm5wdLfweBkOQ7zvMjqHfm/UqvDo"; //Amazon secret key for ID purposes

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
//if an ASIN was enetered, show the info
//otherwise, the code would return alot of errors because there is no ASIN to get info from
if($ASIN!= null){

//print title:
echo "Title: ";
echo $xml->Items->Item->ItemAttributes->Title."<br>";
//Print the word features
echo "<br>"."Features: "."<br>";

for($number = 0; $number < 7; $number= $number +1){
    //there were many features included in the product info,
    //so instead of listing each one I made a for loop
echo $xml->Items->Item->ItemAttributes->Feature[$number]."<br>";

}
//print ASIN
echo "<br>"."ASIN: ";
echo $xml->Items->Item->ASIN."<br>";
//print Manufacturer
echo "<br>". "Manufacturer: ";
echo $xml->Items->Item->ItemAttributes->Manufacturer."<br>";
//print Part number
echo "<br>". "Part Number: ";
echo $xml->Items->Item->ItemAttributes->PartNumber."<br>";
//Print UPC number
echo "<br>". "UPC: ";
echo $xml->Items->Item->ItemAttributes->UPC."<br>";
//print price
echo "<br>". "Formatted Price: ";
echo $xml->Items->Item->ItemAttributes->ListPrice->FormattedPrice."<br>";
//print category
echo "<br>". "Category: ";
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
echo "<br>". "URL for Product: "."<br>";
echo $xml->Items->Item->DetailPageURL."<br>";
//make a clickable product link
echo "<br>"."<a href=".$xml->Items->Item->DetailPageURL.">Product Link</a>";
}}
//execute the above function
ItemLookup();
?>
