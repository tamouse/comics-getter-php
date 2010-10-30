<?php

/**
 * Main page for comicgetter application -- displays most recent comics
 *
 * @author Tamara Temple
 * @version $Id$
 * @copyright Tamara Temple Development, 29 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

require_once('config.inc');

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
$smarty->display('index.tpl');
