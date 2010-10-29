<?php

/**
 * Initialize the data base connection.
 * We're using mysqli OO form
 */

$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_error())
	emit_fatal_error("Could not connect to database " . DBNAME);

