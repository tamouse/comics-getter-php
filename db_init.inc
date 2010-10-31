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

/**
 * Columns to extract from join of COMICSTBL and SUBSCRIPTIONSTBL
 * (csj_ = COMICSTBL-SUBSCRIPTIONSTBL-Join)
 */
$csj_columns[] = 'c.id';
$csj_columns[] = 'c.subscription_id';
$csj_columns[] = 's.name';
$csj_columns[] = 's.uri';
$csj_columns[] = 'c.imgsrc';
$csj_columns[] = 'c.filespec';
$csj_columns[] = 'c.comicdate';
$csj_columns[] = 'c.pulltime';