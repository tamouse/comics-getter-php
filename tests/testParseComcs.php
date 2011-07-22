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

print PHP_EOL.PHP_EOL."Testing parse_comics".PHP_EOL;
$errors=array();
$uri = "http://comics.com/free_range/";
list ($comicdate,$comicuri) = parse_comics($uri);
print "RESULTS FROM parse_comics(): comicdate=$comicdate \$comicuri=$comicuri".PHP_EOL;
foreach ($errors as $error) {
	echo $error;
}
print "END testing parse_comics".PHP_EOL;

$errors=array();
print PHP_EOL.PHP_EOL."Testing parse_gocomics".PHP_EOL;
$uri = "http://www.gocomics.com/2cowsandachicken";
list ($comicdate,$comicuri) = parse_gocomics($uri);
print "RESULTS FROM parse_gocomics(): comicdate=$comicdate, comicuri=$comicuri".PHP_EOL;
foreach ($errors as $error) {
	echo $error;
}
print "END testing parse_gocomics".PHP_EOL;

$errors=array();
print PHP_EOL.PHP_EOL."Testing parse for sinfest.com".PHP_EOL;
$uri = "http://www.sinfest.net/index.php";
list ($comicdate,$comicuri) = parse_sinfest($uri);
print "RESULTS FROM parse_sinfest(): comicdate=$comicdate, comicuri=$comicuri">PHP_EOL;
foreach ($errors as $error) {
	echo $error;
}
print "END testing parse_sinfest".PHP_EOL;

$errors=array();
print PHP_EOL.PHP_EOL."Testing parse_twolumps".PHP_EOL;
$uri="http://twolumps.net/";
list ($comicdate, $imgsrc) = parse_twolumps($uri);
print PHP_EOL."RESULTS FROM parse_twolumps: comicdate=($comicdate), imgsrc=($imgsrc)".PHP_EOL;
foreach ($errors as $error) {
	print $error.PHP_EOL;
}
print "END testing parse_twolumps".PHP_EOL;

print "</pre>".PHP_EOL;

?>