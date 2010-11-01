<?php

/**
 * Configuration file - sample - for comicgetter app
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * @version $Id$
 * @copyright Tamara Temple Development, 28 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

/**
 * Application constants
 */

/**
 * Database connectivity constants.
 * Replace with appropriate values for your local installation
 **/

define("DBHOST",'');
define("DBUSER",'');
define("DBPASS",'');
define("DBNAME",'');

/**
 * Database constants -- do not change unless you change the schema
 **/

define("TBLPREFIX",'cg_');
define("SUBSCRIPTIONSTBL",TBLPREFIX . 'subscriptions');
define("COMICSTBL", TBLPREFIX . 'comics');
define("DELAY_TIME",2); /* # of seconds delay between fetches */

/**
 * Setting some operating values from the query string
 */
$additional_query_parms = Array();

/**
 * Allow debug option to be set on the query string
 **/
if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
	define("DEBUG",FALSE);
	ini_set('display_errors',TRUE);
	ini_set('display_startup_errors',TRUE);
	$additional_query_parms['debug'] = 'true';
} else {
	define("DEBUG",FALSE);	
}

/**
 * Modify behaviour to emit plaintext instead of html
 **/
if ((isset($_GET['periodic']) && $_GET['periodic'] == 'true') || 
	(isset($_GET['nohtml']) && $_GET['nohtml'] == 'true')) {
	define("NOHTML",TRUE);
	header("Content-type: text/plain");
	$additional_query_parms['nohtml'] = 'true';
} else {
	define("NOHTML",FALSE);
}

/*************************************************************************
 * DO NOT CHANGE BELOW UNLESS YOU KNOW WHAT YOU'RE DOING
 *************************************************************************/
define("DIEPREFIX", "<p class=\"error\">");
define("DIESUFFIX", "</p>\n");
define("DEBUGPREFIX", "<p class=\"debug\">");
define("DEBUGSUFFIX", "</p>\n");

/**
 * Include miscelaneous function declarations here (may be used below)
 */
require_once('functions.inc');


define("TEMPDIR","/tmp/");
define("COMICDIR", "comics");
define("MIMETYPE",'mimetype --database=/sw/share/mime -b '); /* flags set for mimetype program */
define("DEFAULT_REDIRECT","index.php");

define("APPBOTTOMDIR",'comicgetter');	/* base directory of application -- if you change the application location or name, you must change this */

/**
 * Get the Application Root directory path. Used for finding files and directories on the server's file system
 */
$script_path_arr = split('/', $_SERVER['SCRIPT_FILENAME']); /* get the elements of the path to the current script */
$app_path_arr = Array();
foreach ($script_path_arr as $script_path_element) {
	$app_path_arr[] = $script_path_element;
	if ($script_path_element == APPBOTTOMDIR) break; /* stop processing when we get to the application base dir */
}
define("APP_ROOT", join('/',$app_path_arr) . '/'); /* path to application root */
//debug("APP_ROOT=".APP_ROOT);

/**
 * Get the Application URI path. Used for creating URI's for elements in the application that are called through the web server
 */
$script_uri_path_arr = split('/', $_SERVER['SCRIPT_NAME']); /* get the elements of the URI path to the current script */
//debug_var("\$script_uri_path_arr",$script_uri_path_arr);
foreach ($script_uri_path_arr as $script_uri_path_element) {
	$app_uri_base_arr[] = $script_uri_path_element;
	if ($script_uri_path_element == APPBOTTOMDIR) break; /* stop processing when we get to the application base dir */
}
//debug_var("\$app_uri_base_arr",$app_uri_base_arr);
define("APP_URI_BASE", join('/',$app_uri_base_arr) . '/'); /* use this in links and images to refer to the application base */
//debug("APP_URI_BASE=".APP_URI_BASE);

define("APP_NAME",'comicgetter');

/**
 * Use this to create fully qualified URLs by adding the path element
 **/
$full_url_parts = array('scheme'=>'http','host'=>$_SERVER['SERVER_NAME'],'port'=>$_SERVER['SERVER_PORT']);


/**
 * Comic services
 */
$parse_engines =
	array(
		array('match'=>'gocomics.com','engine'=>'parse_gocomics'),
		array('match'=>'sinfest.net','engine'=>'parse_sinfest')
	);


/**
 * Common includes
 */

require_once('db_init.inc');
require_once('Smarty.class.php');

/**
 * Set up Smarty
 */
$smarty = new Smarty();
$smarty->template_dir = APP_ROOT . 'templates/';
$smarty->compile_dir = APP_ROOT . 'templates_c/';
//debug_var("\$smarty=",$smarty);
