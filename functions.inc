<?php
/**
 * General functions used in comicgetter app
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
 * debug function - print a message if DEBUG === TRUE
 *
 * @return void
 * @author Tamara Temple
 **/
function debug($msg)
{
	if (DEBUG) {
		echo DEBUGPREFIX . "DEBUG: $msg" . DEBUGSUFFIX;
	}
}

/**
 * Emit a fatal error message to the log and output, and die
 *
 * @return void
 * @author Tamara Temple
 **/
function emit_fatal_error($msg)
{
	error_log(APP_NAME . " FATAL ERROR: $msg");
	die(DIEPREFIX . "FATAL ERROR: $msg" . DIESUFFIX);
}