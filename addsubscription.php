<?php

/**
 * Add a new subscription to the database
 *
 * @author Tamara Temple
 * @version $Id$
 * @copyright Tamara Temple Development, 29 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/
include_once('config.inc');
include_once('HTTP.php');

/**************************************
 * FUNCTIONS
 **************************************/

/**
 * Save the subscription after it's been validated
 *
 * @return id - subscription id of last subscription entered
 * @author Tamara Temple
 **/
function save_subscription($name,$uri)
{
	global $db;
	$sql = "INSERT INTO ".SUBSCRIPTIONSTBL." SET ";
	$columns[]	= '`name`='."'".$name."'";
	$columns[]	= '`uri`='."'".$uri."'";
	$columns[]	= '`created`=NOW()';
	$columns[]	= '`updated`=NOW()';
	$sql .= join(", ",$columns);
	$result = $db->query($sql);
	if ($result === FALSE) {
		emit_fatal_error("Could not insert new subscription into database: \$sql=$sql. error=".$db->error);
	}
	return $db->insert_id;
}


/**
 * validates the name field that was input
 *
 * @return cleaned up $name
 * @author Tamara Temple
 **/
function validatename($name)
{
	global $errors;
	$name = strip_tags($name);
	if (!get_magic_quotes_gpc()) $name = mysqli_real_escape_string($name);
	if (preg_match('/^\s*$/',$name)) {
		/* $name is empty */
		$errors[] = "Name must not be empty.";
		return NULL;
	}
	return $name;
}

/**
 * validates the uri field that was input
 *
 * @return cleaned up $uri
 * @author Tamara Temple
 **/
function validateuri($uri)
{
	global $errors;
	$uri = strip_tags($uri);
	if (preg_match('/^\s*$/',$uri)) {
		/* empty string */
		$errors[]	= 'URI Must not be empty';
		return NULL;
	}
	$uri_parts = parse_url($uri);
	if ($uri_parts === FALSE) {
		$errors[] = "Malformed URI.";
		return NULL;
	}
	$uri = build_url($uri_parts);
	if ($uri === FALSE) {
		/* bad uri formed */
		$errors[] = "Malformed URI.";
		return NULL;
	}
	$headers = HTTP::head($uri); /* Verify that the URI exists */
	if (get_class($headers) == 'PEAR_Error') {
		$errors[] = "Invalid URI.";
		return NULL;
	}
	elseif ($headers['response_code'] != '200') {
		$errors[] = "Invalid URI.";
		return NULL;
	}
	return $uri;
	
}

/**************************************
 * MAIN
 **************************************/

$messages = Array();

if (!empty($_POST)) {
	/* form has been submitted */
	$errors = Array();
	$name = $_POST['comic_name'];
	$name = validatename($name);
	$uri = $_POST['comic_uri'];
	$uri = validateuri($uri);
	if (empty($errors)) {
		/* no validation errors */
		$id = save_subscription($name, $uri);
		$messages[] = "New subsciption saved.";
		$options = array('id' => $id,
						 'messages' => $messages);
		$redirect_url = "Location: subscriptions.php?" . http_build_query($options);
		debug("\$redirect_url=$redirect_url");
		if (DEBUG === FALSE) header($redirect_url);
	}
} else {
	$name = '';
	$uri = '';
}

$subscription_id = 0;

$smarty->assign('messages',$messages);
$smarty->assign('errors',$errors);
$smarty->assign('comic_name',$name);
$smarty->assign('comic_uri',$uri);
$smarty->assign('subscription_id',$subscription_id);
$smarty->assign('title','Add a new subscription');
$smarty->display('addeditsubscriptionform.tpl');