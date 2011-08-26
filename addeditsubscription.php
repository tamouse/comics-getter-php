<?php

/**
 * Add or Edit a subscription to the database
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * @version $Id$
 * @copyright Tamara Temple Development, 29 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/
include_once('config.inc');
include_once('HTTP.php');

debug_var("\$_GET",$_GET);
debug_var("\$_POST",$_POST);

$errors=Array();
$messages=Array();

/**************************************
 * FUNCTIONS
 **************************************/

/**
 * Save the subscription after it's been validated
 *
 * @return id - subscription id of last subscription entered
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function save_subscription($name,$uri,$id=0)
{
	global $db;
	$sql = (ACTION == 'NEW' ? "INSERT INTO" : "UPDATE")." ".SUBSCRIPTIONSTBL." SET ";
	$columns[]	= '`name`='."'".$db->mysqli_real_escape_string($name)."'";
	$columns[]	= '`uri`='."'".$db->mysqli_real_escape_string($uri)."'";
	if (ACTION == 'NEW') $columns[]	= '`created`=NOW()';
	$columns[]	= '`updated`=NOW()';
	$sql .= join(", ",$columns);
	if (ACTION == 'EDIT') {
		$sql .= " WHERE `id`=".$id;
	}
	debug("\$sql=$sql");
	$result = $db->query($sql);
	if (!$result) {
		emit_fatal_error("Could not ".(ACTION == 'NEW' ? "insert new" : "update")." subscription into database: \$sql=$sql. error=".$db->error);
	}
	debug("\$result=$result");
	return (ACTION == 'NEW' ? $db->insert_id : $id);
}



/**
 * validates the name field that was input
 *
 * @return cleaned up $name
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function validatename($name)
{
	global $errors,$messages,$db;
	debug(basename(__FILE__).'@'.__LINE__." name=[$name]");
	$name = strip_tags($name);
	if (!get_magic_quotes_gpc()) $name = mysqli_real_escape_string($db, $name);
	debug(basename(__FILE__).'@'.__LINE__." name=[$name]");
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
 * @author Tamara Temple <tamara@tamaratemple.com>
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
	debug_var("\$headers",$headers);
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

if (!empty($_POST)) {
	/* form has been submitted */
	if (isset($_POST['action_type']) && !empty($_POST['action_type'])) {
		switch ($_POST['action_type']) {
			case 'Add':
				define('ACTION', 'NEW');
				break;
			
			case 'Update':
				define('ACTION', 'EDIT');
				break;
				
			default:
				/* invalid action sent */
				$errors[] = "Invalid action sent in form: ".$_POST['action_type'];
				do_redirect("index.php");
				break;
		}
	}
	$id = (ACTION == 'EDIT' ? $_POST['subscription_id'] : 0);
	$name = $_POST['comic_name'];
	$name = validatename($name);
	$uri = $_POST['comic_uri'];
	$uri = validateuri($uri);
	if (empty($errors)) {
		/* no validation errors */
		$id = save_subscription($name, $uri, $id);
		debug("inserted/updated \$id=$id");
		$additional_query_parms['id'] = $id;
		$messages[] = "subscription saved.";
		do_redirect("subscriptions.php");
	}
} else {
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
				do_redirect("subscriptions.php");
				break;
		}
	}
	debug("ACTION=".ACTION);
	if (ACTION=='EDIT') {
		/* Get the id off the query string */
		if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
			/* invalid id paramter */
			$errors[] = "Invalid subscription id: ".(isset($_GET['id'])?$_GET['id']:"none given");
			do_redirect("subscriptions.php");
		}
		$id = $_GET['id'];
		/* retrieve current subscription info */
		$subscription = get_one_assoc(SUBSCRIPTIONSTBL,$id);
		if (!isset($subscription) || empty($subscription)) {
			$errors[] = "Subscription not found for id: $id";
			do_redirect("subscriptions.php");
		}
		$name = $subscription['name'];
		$uri = $subscription['uri'];
	} else {
		$id = 0;
		$name = '';
		$uri = '';
	}
}


$name = stripslashes($name);
$uri = stripslashes($uri);

if (!empty($additional_query_parms)) {
	$smarty->assign('additional_query_string',http_build_query($additional_query_parms));
}
if (isset($messages)) {
	$smarty->assign('messages',$messages);
}
if (isset($errors)) {
	$smarty->assign('errors',$errors);
}
$smarty->assign('action',"addeditsubscription.php");
$smarty->assign('action_type',(ACTION=='NEW'?'Add':'Update'));
$smarty->assign('comic_name',$name);
$smarty->assign('comic_uri',$uri);
$smarty->assign('subscription_id',$id);
$smarty->assign('title',(ACTION=='NEW'?'Add':'Edit').' a subscription');
$smarty->display('addeditsubscriptionform.tpl');