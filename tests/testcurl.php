<?php

// Test the fetch_url_curl function
header("Content-type: text/plain"); // don't bother with html crap

define("DEBUG",true);
define("DEBUGPREFIX", "");
define("DEBUGSUFFIX", "\n");
define('DEBUGVARPREFIX', '');
define('DEBUGVARSUFFIX', '');

error_reporting(-1);
ini_set('display_errors', 1);


include_once('../functions.inc');


if (!function_exists('fetch_url_curl')) die("Function fetch_url_curl does not exist!");


echo "Beginning test of fetch_url_curl".PHP_EOL;
$url="http://comics.com/free_range/";
echo "url=$url".PHP_EOL;
$contents = fetch_url_curl($url);
if ($contents && !empty($contents)) {
	echo "retrieved $url via curl: ".substr($contents,0,200).PHP_EOL;
} else {
	echo "nothing retrieved from $url via curl".PHP_EOL;
}


?>