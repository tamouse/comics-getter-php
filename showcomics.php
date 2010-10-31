<?php

/**
 * Show all comics for the given subscription
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * @version $Id$
 * @copyright Tamara Temple Development, 31 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

require_once('config.inc');

if (isset($_GET['sid']) && !empty($_GET['sid']) && is_numeric($_GET['sid'])) {
	$sid = $_GET['sid'];
} else {
	$errors[] = __FILE__." (".__LINE__.") ".'Invalid Subscription ID: '.(!isset($_GET['sid']) ? "not set" : (empty($_GET['sid']) ? "empty" : $_GET['sid']));
	do_redirect("index.php");
}

$subscription = get_one_assoc(SUBSCRIPTIONSTBL,$sid);
debug_var("\$subscription=",$subscription);

$sql = "SELECT ";
$sql .= join(", ",$csj_columns);
$sql .= " FROM ".COMICSTBL." as c, ".SUBSCRIPTIONSTBL." as s ";
$whereparts[] = 'c.subscription_id='.$sid;
$whereparts[] = 'c.subscription_id=s.id';
$sql .= " WHERE ".join(" AND ",$whereparts);
$sql .= " ORDER BY c.comicdate desc";
debug("\$sql=$sql");

$result=$db->query($sql);
if ($result === FALSE) {
	$errors[] = __FILE__." (".__LINE__.") "."Get all comics for subscription $sid failed: \$sql=$sql. Error=".$db->error;
	do_redirect("index.php");
}

$comics = Array();
if ($result->num_rows > 0) {
	if (method_exists('mysqli_result','fetch_all')) {
		$comics = $result->fetch_all(MYSQLI_ASSOC);
	} else {
		/* vesion is too old, have to do it by hand */
		while ($row = $result->fetch_assoc()) {
			$comics[] = $row;
		}
	}
}
$result->free();
debug_var("\$comics=",$comics);

$num_comics = count($comics);


$smarty->assign('comics',$comics);
$smarty->assign('num_comics',$num_comics);
$smarty->assign('title',"Comics for ".(isset($subscription['name']) ? $subscription['name'] : "unknown"));
if (isset($messages)) $smarty->assign('messages',$messages);
if (isset($errors)) $smarty->assign('errors',$errors);
if (!empty($additional_query_parms)) $smarty->assign('additional_query_parms',$additional_query_parms);

$smarty->display('showcomics.tpl');

