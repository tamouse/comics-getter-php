<?php
/**
 * General functions used in comicgetter app
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * @version $Id$
 * @copyright Tamara Temple Development, 28 October, 2010
 * @package comicgetter
 *
 * CHANGED: 2011-06-13 tpt added fetch_url_curl function
 **/

/**
 * Define DocBlock
 **/

// ===========
// = DEFINES =
// ===========

if (!defined('DEBUG')) define('DEBUG', FALSE); // in case i forget something


if (!defined('DEBUGPREFIX')) define('DEBUGPREFIX', NOHTML?'':'<p class="debug">'.PHP_EOL);
if (!defined('DEBUGSUFFIX')) define('DEBUGSUFFIX', NOHTML?'':'</p>'.PHP_EOL);
if (!defined('DIEPREFIX')) define('DIEPREFIX', NOHTML?'':'<div class="error">'.PHP_EOL);
if (!defined('DIESUFFIX')) define('DIESUFFIX', NOHTML?'':'</div>');




/**
 * Build a redirect path including messages, errors, and additional query data
 *
 * @return redirect url
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function buildredirect($u)
{
	global $errors, $messages, $additional_query_parms;
	
	$redirect = $u;
	$options = Array();
	if (isset($additional_query_parms) && !empty($additional_query_parms)) {
		$options = $additional_query_parms;
	}
	if (isset($messages) && !empty($messages)) {
		$options['messages'] = $messages;
	}
	if (isset($errors) && !empty($errors)) {
		$options['errors'] = $errors;
	}
	debug_var("\$options:",$options);
	if (!empty($options)) {
		$redirect .= "?" . http_build_query($options);
	}
	return $redirect;
	
}

/**
 * Build a url string based on the parts given
 *
 * @return url string
 * @author Tamara Temple <tamara@tamaratemple.com>
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
	if (isset($parts['path_prefix'])) {
		$uri .= $parts['path_prefix'];
	}
	if (isset($parts['path'])) {
		$uri .= $parts['path'];
	} else {
		$uri .= '/';
	}
	if (isset($parts['path_suffix'])) {
		$uri .= $parts['path_suffix'];
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
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function debug($msg,$var='',$file='',$line='')
{
	if (DEBUG) {;
		$out = '';
		echo DEBUGPREFIX.PHP_EOL;
		$file = basename($file);
		if (!empty($file)) $out .= $file;
		if (!empty($line)) $out .= '@'.$line;
		$out .= "DEBUG: $msg".PHP_EOL;
		if (!empty($var)) {
			if (is_array($var)) {
				$out .= NOHTML?print_r($var,true):htmlspecialchars(print_r($var,true));				
			} else {
				$out .= NOHTML?$var:htmlspecialchars($var);
			}
		}
		echo $out;
		error_log($out);
		echo DEBUGSUFFIX.PHP_EOL;
	}
}

/**
 * debug var function - DEPRICATED - use debug instead now
 **/
function debug_var($msg,$var='',$file='',$line='')
{
	debug($msg,$var,$file,$line);
}

/**
 * perform a redirect to the indicated url $u, applying other paramters as needed.
 *
 * @return none - will either redirect or exit
 * @author Tamara Temple
 **/
function do_redirect($u)
{
	if (!isset($u)) $u = DEFAULT_REDIRECT;
	$u = buildredirect($u);
	debug("Redirect: \$u=$u");
	if (!DEBUG) header("Location: $u"); else exit("<p><a href='$u'>Redirect to $u</a></p>");
}

/**
 * Emit a fatal error message to the log and output, and die
 *
 * @return void
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function emit_fatal_error($msg)
{
	error_log(APP_NAME . " FATAL ERROR: $msg");
	die(DIEPREFIX . "FATAL ERROR: $msg" . DIESUFFIX);
}

/**
 * fetch the url using curl
 *
 * @return (string) contents of url
 * @author Tamara Temple
 **/
function fetch_url_curl($url)
{
	global $errors;
	
	if (!isset($url) || empty($url)) return NULL;
	
	$ch = curl_init($url);
	$options = array(
		CURLOPT_AUTOREFERER => TRUE,
		CURLOPT_COOKIESESSION => TRUE,
		CURLOPT_FAILONERROR => TRUE,
		CURLOPT_FOLLOWLOCATION => TRUE,
		CURLOPT_HEADER => FALSE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_CONNECTTIMEOUT => 30,
		CURLOPT_TIMEOUT => 200,
		CURLOPT_USERAGENT => 'Mozilla', // do this to fake out servers that won't do anything unless they recognize the request coming from a browser
		
	);
	curl_setopt_array($ch, $options);
	$contents = curl_exec($ch);
	if (FALSE === $contents) {
		$errors[]="Unable to fetch contents of $url.";
		return NULL;
	}
	curl_close($ch);
	
	return $contents;
	
}

/**
 * Get All records from a table, returning them in an indexed array of records as associative arrays
 *
 * @return indexed array of records as associative arrays
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function get_all_array($tblname,$options=NULL)
{
	global $db;
	if (isset($options)) {
		debug_var("get_all_array \$options:",$options);
		foreach ($options as $key => $value) {
			switch ($key) {
				case 'sort':
					if (is_array($value)) {
						$orderparts = $value;
					} else {
						$orderparts[] = $value;
					}
					break;
				
				case 'where':
					if (is_array($value)) {
						$whereparts = $value;
					} else {
						$whereparts[] = $value;
					}
					break;
					
				default:
					# code...
					break;
			}
		}
	}
	$sql = "SELECT * FROM $tblname";
	if (isset($whereparts)) $sql .= " WHERE ".join(" AND ",$whereparts);
	if (isset($orderparts)) $sql .= " ORDER BY ".join(",",$orderparts);
	debug("get_all_array \$sql=$sql");
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
	return $all_rows;
}

/**
 * Get the last comic pulled for a given subscription
 *
 * @return date of last comic pulled
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function get_last_comic_pulled($subscription_id)
{
	global $db,$csj_columns;
	$sql 		= "SELECT ";
	$sql 		.= join(", ", $csj_columns);
	$sql 		.= " FROM " . COMICSTBL . " as c, " . SUBSCRIPTIONSTBL . " as s ";
	$sql		.= " WHERE ";
	$wherebits[] = "comicdate=(select MAX(comicdate) from ".COMICSTBL." where subscription_id=".$subscription_id.")";
	$wherebits[] = "c.subscription_id=" . $subscription_id;
	$wherebits[] = "c.subscription_id=s.id";
	$sql		.= join(" AND ",$wherebits);
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
 * Get a single record given the $id of the record
 *
 * @return single record returned as associative array
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function get_one_assoc($tbl,$id)
{
	global $db;
	$sql = "SELECT * FROM ".$tbl." WHERE `id`=".$id." LIMIT 1";
	$result = $db->query($sql);
	if ($result === FALSE) emit_fatal_error("Could not execute \$sql=$sql: error=".$db->error);
	$row = $result->fetch_assoc();
	$result->free();
	return $row;
}

/**
 * Retrieve the parsing engine for the given url $u
 *
 * @return $engine - function to use to parse the url $u
 * @author Tamara Temple <tamara@tamaratemple.com>
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
 * Return the referer of this page, removing the query string and application path info
 *
 * @return $referer 
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function get_referer()
{
	global $errors;
	if (!isset($_SERVER['HTTP_REFERER'])) return DEFAULT_RETURN;
	debug("HTTP_REFERER=".$_SERVER['HTTP_REFERER']);
	$referer = $_SERVER['HTTP_REFERER'];
	if (!preg_match('!'.APP_URI_BASE.'!',$referer)) {
		$errors[] = 'Referer not from this application';
		return DEFAULT_RETURN;
	}
	$referer = preg_replace('/\?.*/','',$referer);
	if (!isset($referer)) {
		$errors[] = 'Error occured when cleaning HTTP_REFERER in get_referer';
		return DEFAULT_RETURN;
	}
	$referer = preg_replace('!^[a-z]+://[^/]*'.APP_URI_BASE.'!','',$referer);
	if (!isset($referer)) {
		$errors[] = 'Error occured when cleaning up path in get_referer';
		return DEFAULT_RETURN;
	}
	debug("\$referer=$referer");
	return $referer;
}

/**
 * Retrieve a record containing comic info for the given comic id
 *
 * @return comic record as associative array
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function get_this_comic($comic_id)
{
	global $db,$csj_columns;
	$sql 		= "SELECT ";
	$sql 		.= join(', ', $csj_columns);
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