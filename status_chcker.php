<?php
//YOU CAN EDIT THESE
//THIS IS YOUR PHONENUMBER, No hyphens!

$my_phone = "8603872247";

//This is your phone service, enter in the number of the one you use from the list before
//1 = Sprint , 2 = Verizon , 3 = T-Mobile, 4 = AT&T 
$my_carrier = "1";

//DON'T EDIT ANYTHING BELOW.

//These are the list of available SMS gateways, you actually can add more
//The list is available here : http://en.wikipedia.org/wiki/List_of_SMS_gateways
$carrier[1] = "messaging.sprintpcs.com";
$carrier[2] = "vtext.com";
$carrier[3] = "tmomail.net";
$carrier[4] = "txt.att.net";

//This is where the json variable gets used
//change this line to 1 if you want it to always print in json
$print_json = 0;

//if you've set the above to 1, comment out this line
$getJson = htmlspecialchars($_GET[json],ENT_NOQUOTES);
if($getJson){ $print_json = 1; }

//Debug
$getDebug = htmlspecialchars($_GET[debug],ENT_NOQUOTES);
if($getDebug){ $print_debug = 1; }

//FOR THE LOVE OF ALL THAT IS HOLY, DO NOT EDIT ANYTHING BELOW THIS LINE
//This is where I start to print out the json header (If the above is 1)
if($getJson){ 
header('Content-type: application/json');
echo"{\n\"addresses\": [\n";
}

//This opens the .txt file of the ip list
$file = fopen("iplist.txt", "r");
$members = array();
$x=0;
$offline_servers=0;
while(!feof($file)) {
$ip_name = fgets($file);
$ip = explode(":", $ip_name);
$online = @fsockopen( $ip[0], $ip[1], $errno, $errstr, 200);
$x++;
if($online >= 1) {
$servers = "$servers , $ip_name";
$status = "<span style=\"color:green;\">online</span>";
$status_clean = "offline";
}
else {
$status = "<span style=\"color:red;\">offline</span>";
$status_clean = "offline";
$servers = "$servers , $ip_name";
$offline_servers++;
}
$ip_name = str_replace(array("\r\n", "\r", "\n"),"",$ip_name);
if($getJson){ echo"{ \"ip\":\"$ip_name\" , \"status\":\"$status_clean\" },\n"; }else{ echo "$ip_name : $status<br/>"; }
}
//Json end catch, badly written perhaps, but it has to be there! Also it doesn't affect any json queries since it doesn't match the other outputs.
if($getJson){ echo"{ \"completed\":\"yes\" }\n]\n} "; }

if($offline_servers>=1){
mail("$my_phone@$carrier[$my_carrier]", "ALERT THE FOLLOWING ARE OFFLINE", "$servers");
}

if($print_debug){ echo" Phone : $my_phone , Carrier : $my_phone@$carrier[$my_carrier] , Offline : $offline_servers"; }

fclose($file);

?>
