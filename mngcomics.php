<?php

/**
 * mngcomics - manage comic collections
 *
 * @author Tamara Temple tamouse@gmail.com
 * @version $Id$
 * @copyright Tamara Temple Development, 31 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

require_once('config.inc');
$messages = Array();
$errors = Array();

/**
 * Get all the subscriptions
 */
$subscriptions = get_all_array(SUBSCRIPTIONSTBL,array('sort'=>'name'));
if (!isset($subscriptions) || empty($subscriptions)) {
	$errors = "No subscriptions found!";
	do_redirect("index.php");
}

/**
 * Get the comics for each subscription
 */
$comiclist = Array();
foreach ($subscriptions as $index => $subscription) {

	$sql = "SELECT ";
	$columns = Array();
	$sql .= join(", ",$csj_columns);
	$sql .= " FROM ".COMICSTBL." as c, ".SUBSCRIPTIONSTBL." as s ";
	$where = Array();
	$where[] = 'c.subscription_id='.$subscription['id'];
	$where[] = 'c.subscription_id=s.id';
	$sql .= " WHERE ".join(" AND ",$where);
	$sql .= " ORDER BY c.comicdate";
	
	debug("\$sql=$sql");
	
	$result=$db->query($sql);
	
	if (!$result) {
		emit_fatal_error("Error in obtaining comics for subscription id=".$subscription['id']." \$sql=$sql; error=".$db->error);
	}
	
	$comiclist[$subscription['name']] = Array(); /* initialize the subarray for this subscription */
	while ($row = $result->fetch_assoc()) {
		$comiclist[$subscription['name']][] = $row;
	}
	$result->free();
	
}

debug_var("\$comiclist",$comiclist);


if (!empty($messages)) $smarty->assign('messages',$messages);
if (!empty($errors)) $smarty->assign('errors',$errors);
if (!empty($additional_query_parms)) {
	$smarty->assign('additional_query_string',http_build_query($additional_query_parms));
	$smarty->assign('additional_query_parms',$additional_query_parms); /* need both because of form submit with get option in template */
}

$smarty->assign('title','Manage Comics');
$smarty->assign('comiclist',$comiclist);
$smarty->display('mngcomics.tpl');

