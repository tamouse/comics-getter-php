<?php

/**
 * Main page for comicgetter application -- displays most recent comics
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

if (isset($_GET['messages'])) $messages = $_GET['messages'];
if (isset($_GET['errors'])) $errors = $_GET['errors'];

$subscriptions = get_all_array(SUBSCRIPTIONSTBL);
$comics = Array();
foreach ($subscriptions as $key => $subscription) {
	$comic = get_last_comic_pulled($subscription['id']);
	if (isset($comic)) $comics[] = $comic;
}
debug_var("\$comics=",$comics);

$smarty->assign('additional_query_string',http_build_query($additional_query_parms));

$smarty->assign('title','Latest Comics');
$smarty->assign('num_comics',count($comics));
$smarty->assign('comics', $comics);
if (isset($messages)) $smarty->assign('messages',$messages);
if (isset($errors)) $smarty->assign('errors',$errors);
$smarty->display('index.tpl');
