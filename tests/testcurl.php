<?php
/**
 * testcurl
 *
 * @author Tamara Temple tamouse@gmail.com
 * @version \$Id\$
 * @copyright 2010 Tamara Temple Development
 * @package default
 **/

/**
 * Define DocBlock
 **/

define('TEMPDIR', '/tmp/');
define("MIMETYPE",'mimetype --database=/sw/share/mime -b '); /* flags set for mimetype program */

function testcurl($imguri)
{
	$ch = curl_init();
	$fn = tempnam(TEMPDIR, "testcomic");
	$fh = fopen($fn,'w');
	$options = Array(
		CURLOPT_URL => $imguri,
		CURLOPT_USERAGENT => "Mozilla/5.0",
		CURLOPT_FILE => $fh,
		CURLOPT_HEADER => FALSE,
		CURLOPT_FOLLOWLOCATION => TRUE,
		CURLOPT_MAXREDIRS => '10'
		);
	curl_setopt_array($ch, $options);
	if (curl_exec($ch) === FALSE) {
		$error_msg = "Unable to retrieve $imgurl: ".curl_error($ch);
		error_log($error_msg);
		$messages[] = $error_msg;
	}
	fclose($fh);
	curl_close($ch);
	$cmd = MIMETYPE." ".escapeshellcmd($fn)." 2>/dev/null";
	$result = `$cmd`;
	echo "$fn mimetype is $result";
}

testcurl("http://sinfest.net/comikaze/comics/2010-10-31.gif");
?>