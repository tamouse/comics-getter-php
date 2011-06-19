<?php

// Test the parse_comics function in parserengines.inc

define("DEBUG",true);
define("DEBUGPREFIX", "");
define("DEBUGSUFFIX", "\n");
define('DEBUGVARPREFIX', '');
define('DEBUGVARSUFFIX', '');

error_reporting(-1);
ini_set('display_errors', 1);


include("../functions.inc");
include("../parserengines.inc");

print "<pre>".PHP_EOL;
debug("Debug should be on");

print PHP_EOL.PHP_EOL."Testing parse for comics.com".PHP_EOL;
$errors=array();
$uri = "http://comics.com/free_range/";
list ($comicdate,$comicuri) = parse_comics($uri);
print "RESULTS FROM parse_comics(): comicdate=$comicdate \$comicuri=$comicuri".PHP_EOL;
foreach ($errors as $error) {
	echo $error;
}

$errors=array();
print PHP_EOL.PHP_EOL."Testing part for gocomics.com".PHP_EOL;
$uri = "http://www.gocomics.com/2cowsandachicken";
list ($comicdate,$comicuri) = parse_gocomics($uri);
print "RESULTS FROM parse_gocomics(): comicdate=$comicdate, comicuri=$comicuri".PHP_EOL;
foreach ($errors as $error) {
	echo $error;
}

$errors=array();
print PHP_EOL.PHP_EOL."Testing parse for sinfest.com".PHP_EOL;
$uri = "http://www.sinfest.net/index.php";
list ($comicdate,$comicuri) = parse_sinfest($uri);
print "RESULTS FROM parse_sinfest(): comicdate=$comicdate, comicuri=$comicuri">PHP_EOL;
foreach ($errors as $error) {
	echo $error;
}

print "</pre>".PHP_EOL;
?>