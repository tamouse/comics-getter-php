<?php

/**
 * Manage subscriptions in the database
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
 * handle CRUD on comic subscriptions
 */

require_once('config.inc');

if (isset($_GET['messages'])) {
	$messages = $_GET['messages'];
} else {
	$messages = Array();
}

$subscriptions = get_all_array(SUBSCRIPTIONSTBL);

$smarty->assign('additional_query_string',http_build_query($additional_query_parms));
$smarty->assign('messages',$messages);
$smarty->assign('subscriptions', $subscriptions);
$smarty->assign('title', "Manage Subscriptions");
$smarty->display('subscriptions.tpl');