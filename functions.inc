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
 * Build a url string based on the parts given
 *
 * @return url string
 * @author Tamara Temple
 **/
function build_url($parts)
{
	$uri = (isset($parts['schema']) ? $parts['schema'] : 'http') . '://';
	if (isset($parts['user'])) {
		$uri .= $parts['user'];
		if (isset($parts['pass'])) {
			$uri .= ':' . $parts['pass'];
		}
		$uri .= '@';
	}
	if (isset($parts['host'])) {
		$uri .= $parts['host'];
	} else {
		return NULL; /* no host given, bogus url */
	}
	if (isset($parts['port'])) {
		$uri .= ":".$parts['port'];
	}
	if (isset($parts['path'])) {
		$uri .= $parts['path'];
	} else {
		$uri .= '/';
	}
	if (isset($parts['query'])) {
		$uri .= '?' . $parts['query'];
	}
	if (isset($parts['fragment'])) {
		$uri .= '#' . $parts['fragment'];
	}
	return $uri;
}

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
 * debug var function - dump a variable if DEBUG === TRUE
 *
 * @return void
 * @author Tamara Temple
 **/
function debug_var($msg,$var)
{
	if (DEBUG) {
		echo DEBUGPREFIX . "DEBUG: $msg" . DEBUGSUFFIX;
		echo "<pre>\n";
		print_r($var);
		echo "</pre>\n";
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


/**
 * Get All records from a table, returning them in an indexed array of records as associative arrays
 *
 * @return indexed array of records as associative arrays
 * @author Tamara Temple
 **/
function get_all_array($tblname)
{
	global $db;
	$sql = "SELECT * FROM $tblname";
	$result=$db->query($sql);
	if ($result === FALSE) emit_fatal_error("Could not retrieve rows from $tblname. \$sql=$sql. error=".$db->error);
	$all_rows = Array();
	if ($result->num_rows > 0) {
		if (method_exists('mysqli_result','fetch_all')) {
			$all_rows = $result->fetch_all(MYSQLI_ASSOC);
		} else {
			/* vesion is too old, have to do it by hand */
			while ($row = $result->fetch_assoc()) {
				$all_rows[] = $row;
			}
		}
	}
	$result->free();
	debug_var("All rows returned from get_all_array(".$tblname.")",$all_rows);
	return $all_rows;
}

/**
 * Get the last comic pulled for a given subscription
 *
 * @return date of last comic pulled
 * @author Tamara Temple
 **/
function get_last_comic_pulled($subscription_id)
{
	global $db;
	$sql 		= "SELECT ";
	$columns[]	= 'c.`id`';
	$columns[]	= 'c.`subscription_id`';
	$columns[]	= 's.`name`';
	$columns[]	= 's.`uri`';
	$columns[]	= 'c.`imgsrc`';
	$columns[]	= 'c.`filespec`';
	$columns[]	= 'MAX(c.`comicdate`) as comicdate';
	$columns[]	= 'c.`pulltime`';
	$sql 		.= join(", ", $columns);
	$sql 		.= " FROM " . COMICSTBL . " as c, " . SUBSCRIPTIONSTBL . " as s ";
	$sql		.= " WHERE c.subscription_id=" . $subscription_id . " AND c.subscription_id=s.id ";
	$sql		.= " GROUP BY s.name ";
	$sql		.= " LIMIT 1";
	$result = $db->query($sql);
	if ($result === FALSE) 
		emit_fatal_error("Error selecting last comic from " . COMICSTBL . ": \$sql=$sql ; error: " . $db->error);
	if (isset($result) && $result->num_rows > 0) {
		$row = $result->fetch_assoc();
	} else {
		$row = NULL;
	}
	$result->free();
	return $row;
}

/**
 * Get a single record given the $sql input
 *
 * @return single record returned as associative array
 * @author Tamara Temple
 **/
function get_one_assoc($sql)
{
	global $db;
	$result = $db->query($sql);
	if ($result === FALSE) emit_fatal_error("Could not execute \$sql=$sql: error=".$db->error);
	$row = $result->fetch_assoc();
	$result->free();
	debug_var("Row returned from $sql:",$row);
	return $row;
}

/**
 * Retrieve the parsing engine for the given url $u
 *
 * @return $engine - function to use to parse the url $u
 * @author Tamara Temple
 **/
function get_parse_engine($u)
{
	global $parse_engines;
	$engine = NULL;
	foreach ($parse_engines as $key => $parse_engine) {
		if (preg_match('/'.$parse_engine['match'].'/i',$u)) {
			$engine = $parse_engine['engine'];
			break;
		}
	}
	return $engine;
}

/**
 * Retrieve a record containing comic info for the given comic id
 *
 * @return comic record as associative array
 * @author Tamara Temple
 **/
function get_this_comic($comic_id)
{
	global $db;
	$sql 		= "SELECT ";
	$columns[]	= 'c.`id`';
	$columns[]	= 'c.`subscription_id`';
	$columns[]	= 's.`name`';
	$columns[]	= 's.`uri`';
	$columns[]	= 'c.`imgsrc`';
	$columns[]	= 'c.`filespec`';
	$columns[]	= 'c.`comicdate`';
	$columns[]	= 'c.`pulltime`';
	$sql 		.= join(', ', $columns);
	$sql 		.= ' FROM ' . COMICSTBL . ' as c, ' . SUBSCRIPTIONSTBL . ' as s ';
	$sql		.= ' WHERE ';
	$where_parts[] = 'c.`id`=' . $comic_id;
	$where_parts[] = 'c.`subscription_id`=s.`id`';
	$sql		.= join(" AND ",$where_parts);
	$sql		.= ' GROUP BY s.`name` ';
	$sql		.= " LIMIT 1";
	$result = $db->query($sql);
	if ($result === FALSE) 
		emit_fatal_error("Error selecting last comic from " . COMICSTBL . ": \$sql=$sql ; error: " . $db->error);
	if (isset($result) && $result->num_rows > 0) {
		$row = $result->fetch_assoc();
	} else {
		$row = NULL;
	}
	$result->free();
	return $row;
	
}