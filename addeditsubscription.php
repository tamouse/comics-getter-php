<?php

/**
 * Add or Edit a subscription to the database
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

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'new':
			define("ACTION","NEW");
			break;

		case 'edit':
			define("ACTION","EDIT");
			break;
			
		default:
			$errors[] = "Invalid action given.";
			$redirect = buildredirect("subscriptions.php");
			header("Location: ".$redirect);
			break;
	}
}

/**************************************
 * FUNCTIONS
 **************************************/

/**
 * Save the subscription after it's been validated
 *
 * @return id - subscription id of last subscription entered
 * @author Tamara Temple
 **/
function save_subscription($name,$uri,$id=0)
{
	global $db;
	$sql = (ACTION == 'new' ? "INSERT" : "UPDATE")." INTO ".SUBSCRIPTIONSTBL." SET ";
	if (ACTION == 'edit') $columns[] = '`id`='.$id;
	$columns[]	= '`name`='."'".$name."'";
	$columns[]	= '`uri`='."'".$uri."'";
	if (ACTION == 'new') $columns[]	= '`created`=NOW()';
	$columns[]	= '`updated`=NOW()';
	$sql .= join(", ",$columns);
	if (ACTION == 'edit') {
		$sql .= " WHERE `id`=".$id;
	}
	$result = $db->query($sql);
	if ($result === FALSE) {
		emit_fatal_error("Could not ".(ACTION == 'new' ? "insert new" : "update")." subscription into database: \$sql=$sql. error=".$db->error);
	}
	return (ACTION == 'new' ? $db->insert_id : $id);
}



/**
 * validates the name field that was input
 *
 * @return cleaned up $name
 * @author Tamara Temple
 **/
function validatename($name)
{
	global $errors,$messages;
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
	global $errors,$messages;
	$uri = strip_tags($uri);
	$uri_parts = parse_url($uri);
	if ($uri_parts === FALSE) {
		$errors[] = "Malformed URI.";
		return NULL;
	}
	$uri = build_url($uri_parts);
	if (!isset($uri)) {
		/* bad uri formed */
		$errors[] = "Malformed URI.";
		return NULL;
	}
	if (empty($uri) || preg_match('/^\s*$/',$uri)) {
		/* empty string */
		$errors[]	= 'URI Must not be empty';
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
	if (!get_parse_engine($uri)) {
		$messages[] = "No parse engine for $uri. No comics will be retrieved.";
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
	$id = (ACTION == 'edit' ? $_POST['id'] : 0);
	$name = $_POST['comic_name'];
	$name = validatename($name);
	$uri = $_POST['comic_uri'];
	$uri = validateuri($uri);
	if (empty($errors)) {
		/* no validation errors */
		$id = save_subscription($name, $uri, $id);
		$messages[] = "subscription saved.";
		$redirect = buildredirect("subscriptions.php");
		debug("\$redirect_url=$redirect_url");
		if (DEBUG === FALSE) header("Location: ".$redirect_url);
	}
} else {
	$name = '';
	$uri = '';
}

$subscription_id = 0;
$name = stripslashes($name);
$uri = stripslashes($uri);

if (isset($redirect_url)) {
	$smarty->assign('redirect_url',$redirect_url);
}
if (isset($redirect_target)) {
	$smarty->assign('redirect_target',$redirect_target);
}
if (!empty($additional_query_parms)) {
	$smarty->assign('additional_query_string',http_build_query($additional_query_parms));
}
if (isset($messages)) {
	$smarty->assign('messages',$messages);
}
if (isset($errors)) {
	$smarty->assign('errors',$errors);
}
$smarty->assign('action',htmlentities($_SERVER['PHP_SELF']));
$smarty->assign('action_type',(ACTION=='new'?'Add':'Update'));
$smarty->assign('comic_name',$name);
$smarty->assign('comic_uri',$uri);
$smarty->assign('subscription_id',$subscription_id);
$smarty->assign('title',(ACTION=='new'?'Add':'Edit').' a subscription');
$smarty->display('addeditsubscriptionform.tpl');