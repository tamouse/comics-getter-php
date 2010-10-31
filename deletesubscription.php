<?php

/**
 * Delete a subscription from the database
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * @version $Id$
 * @copyright Tamara Temple Development, 29 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

require_once('config.inc');

/************************************************
 * FUNCTIONS
 ************************************************/

/**
 * Delete the subscription given by the $id
 *
 * @return success or failure
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function deletesubscription($id=0)
{
	global $db,$errors;
	if (isset($id) && $id > 0) {
		$sql = "DELETE FROM ".SUBSCRIPTIONSTBL." WHERE `id`=".$id;
		$result = $db->query($sql);
		if ($result === FALSE) {
			$errors[] = "Could not delete subscription id=$id. \$sql=$sql. Error: ".$db->error;
		}
		return $result;
	} else {
		$errors[] = 'Invalid subscription id.';
		return FALSE;
	}
}

/************************************************
 * MAIN
 ************************************************/
debug_var("\$_POST",$_POST);
if (!empty($_POST)) {
	/* second pass -form has been submitted */
	$id = (isset($_POST['id']) ? $_POST['id'] : 0);
	$confirm = (isset($_POST['confirm']) ? $_POST['confirm'] : 'no');
	switch ($confirm) {
		case 'yes':
			$result = deletesubscription($id);
			if (!$result) {
				$messages[] = "Subscription not deleted.";
			} else {
				$messages[] = "Subscription deleted.";
			}
			break;
		
		default:
			$messages[] = "Delete canceled.";
			break;
	}
	do_redirect("subscriptions.php");
} else {
	/* first pass -form not yet submitted */
	if (!isset($_GET['id']) || $_GET['id'] < 1) {
		/* invalid id */
		$errors[] = 'Invalid subscription id';
		do_redirect("subscriptions.php");
	} else {
		$id = $_GET['id'];
		$subscription = get_one_assoc(SUBSCRIPTIONSTBL,$id);
		if (!isset($subscription)) {
			$errors[] = "Could not find id=$id in database.";
			do_redirect("subscriptions.php");
		}
	}
}
$smarty->assign('title','Delete subscription');
if (isset($subscription)) $smarty->assign('subscription',$subscription);
if (!empty($additional_query_parms)) {
	$smarty->assign('additional_query_string',http_build_query($additional_query_parms));
}
if (isset($messages)) $smarty->assign('messages',$messages);
if (isset($errors)) $smarty->assign('errors',$errors);
$smarty->display('deletesubscription.tpl');
