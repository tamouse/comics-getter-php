<?php

/**
 * Configuration file - sample - for comicgetter app
 *
 * @author Tamara Temple
 * @version $Id$
 * @copyright Tamara Temple Development, 28 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

/**
 * Database connectivity constants.
 * Replace with appropriate values for your local installation
 **/

define(DBHOST,'');
define(DBUSER,'');
define(DBPASS,'');
define(DBNAME,'');

/**
 * Database constants -- do not change unless you change the schema
 **/

define(TBLPREFIX,'cg_');
define(SUBSCRIPTIONSTBL,TBLPREFIX . 'subscriptions');
define(COMICSTBL, TBLPREFIX . 'comics');

/*************************************************************************
 * DO NOT CHANGE BELOW UNLESS YOU KNOW WHAT YOU'RE DOING
 *************************************************************************/

/**
 * Application constants
 */
define(APPBASE,'comicgetter');	/* base directory of application -- if you change the application location or name, you must change this */
$script_path_arr = split('/', $_SERVER['SCRIPT_FILENAME']); /* get the elements of the path to the current script name */
$app_path_arr = Array();
foreach ($script_path_arr as $script_path_element) {
	$app_path_arr[] = $script_path_element;
	if ($scipt_path_element == APPBASE) break; /* stop processing when we get to the application base dir */
}
define(APP_ROOT, join('/',$app_path_arr) . '/'); /* path to application root */

define(DIEPREFIX, "<p class=\"error\">");
define(DIESUFFIX, "</p>\n");
define(DEBUGPREFIX, "<p class=\"debug\">");
define(DEBUGSUFFIX, "</p>\n");

/**
 * Comic services
 */
define(GOCOMICS,'www.gocomics.com');


/**
 * Common includes
 */
require_once('db_init.inc');
require_once('functions.inc');
require_once('Smarty.class.php');

/**
 * Globals
 */
$smarty = new Smarty();
$smarty->template_dir = APP_ROOT . 'templates';
$smarty->compile_dir = APP_ROOT . 'templates_c';
