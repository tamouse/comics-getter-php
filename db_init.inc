<?php

/**
 * db_init - initialize the database connection
 *
 * @author Tamara Temple tamouse@gmail.com
 * @version $Id$
 * @copyright Tamara Temple Development, 31 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

/**
 * Initialize the data base connection.
 * We're using mysqli OO form
 */

$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_error())
	emit_fatal_error("Could not connect to database " . DBNAME);

