<?php
require("phpsqlsearch_dbinfo.php");


$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];											// Get parameters from URL
$radius = $_GET["radius"];
$stype = $_GET["stype"];


$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");								// Start XML file, create parent node
$parnode = $dom->appendChild($node);


$connection=mysql_connect (localhost, $username, $password);
if (!$connection) {													// Opens a connection to a mySQL server
  die("Not connected : " . mysql_error());
}


$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {												// Set the active mySQL database
  die ("Can\'t use db : " . mysql_error());
}

// Search the rows in the markers table
$query = sprintf("SELECT address, name, lat, lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20 AND Type = stype" ,
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($center_lng),
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($radius));
$result = mysql_query($query);

$result = mysql_query($query);
if (!$result) {
  die("Invalid query: " . mysql_error());
}

header("Content-type: text/xml");


while ($row = @mysql_fetch_assoc($result)){
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("name", $row['name']);
  $newnode->setAttribute("address", $row['address']);            // Iterate through the rows, adding XML nodes for each
  $newnode->setAttribute("lat", $row['lat']);
  $newnode->setAttribute("lng", $row['lng']);
  $newnode->setAttribute("City", $row['City']);
}

echo $dom->saveXML();
?>