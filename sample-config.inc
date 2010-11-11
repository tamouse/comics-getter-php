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

define('APP_ROOT', dirname(__FILE__));
define('APP_URI_BASE', isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME'] : ''));

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
