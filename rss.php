<?php

/**
 * Generate an RSS feed of the latest comics
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

header("Content-type: application/rss+xml");

$subscriptions = get_all_array(SUBSCRIPTIONSTBL); /* retrieve all the current subscriptons */
if (empty($subscriptions)) exit;

/**
 * Build the set of latest comics for each subscription
 */
$comics = Array();
foreach ($subscriptions as $index => $subscription) {
	$comic = get_last_comic_pulled($subscription['id']);
	if (isset($comic)) {
		$comic['filesize'] = filesize(APP_ROOT.$comic['filespec']);
		$cmd = MIMETYPE." ".escapeshellcmd(APP_ROOT.$comic['filespec'])." 2>/dev/null";
		$mimetype = `$cmd`;
		$comic['filetype'] = rtrim($mimetype);
		$full_url_parts['path'] = APP_URI_BASE.$comic['filespec'];
		$comic['fullurl'] = build_url($full_url_parts);
		$comic['pubdate'] = date("r",strtotime($comic['comicdate']));
		debug_var("\$comic after updates in ".__FILE__,$comic);
		$comics[] = $comic;
	}
}

/**
 * Set up the variables for Smarty and invoke the template
 */
if (!empty($comics)) {
	$smarty->assign('title','Latest Comics');
	$full_url_parts['path']=APP_URI_BASE."index.php";
	$smarty->assign('link',build_url($full_url_parts));
	$full_url_parts['path']=APP_URI_BASE."rss.php";
	$smarty->assign('atomlink',build_url($full_url_parts));
	$smarty->assign('description','Comic display engine');
	$smarty->assign('language','en-us');
	$smarty->assign('comics',$comics);
	$smarty->display('rss.tpl');
}
