<?php
/**
 * Get comics from service
 *
 * Different comic web services have different layouts, so must be parsed differenlty.
 * Based on the comic's URI, the service will be determined and the appropriate routine to parse
 * the returned HTML will be called, rendering the current comic's image source URI.
 * The image will be retrieved and stored locally, with the stored image's file path saved in the data base.
 *
 * @author Tamara Temple
 * @version $Id$
 * @copyright Tamara Temple Development, 28 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

require_once('config.inc');
require_once('gocomics.inc');

/*********************************************************
 * FUNCTIONS
 *********************************************************/

/**
 * Determine the extension of the file by examining the output from the system command file(1)
 *
 * @return extension - string
 * @author Tamara Temple
 **/
function determine_extension($fn)
{
	$cmd = "file \"".escapeshellcmd($fn)."\"";
	$result = `$cmd`;
	$found = preg_match('/(gif|jpe?g|png)/i',$result, $matches);
	if ($found) {
		$ext=strtolower($matches[0]);
		$ext=preg_replace('/jpeg/i','jpg',$ext);
	} else {
		$ext='dat';
	}
	return $ext;
}



/**
 * Pull the comic image and save locally
 *
 * @return file_spec - APP_ROOT relative path to file retrieved
 * @author Tamara Temple
 **/
function pull_comic($name, $date, $imguri)
{
	global $messages;
	$ch = curl_init();
	$fn = tempnam(TEMPDIR, "comic");
	debug("\$fn=$fn");
	$fh = fopen($fn,'w');
	$options = Array(
		CURLOPT_URL => $imguri,
		CURLOPT_USERAGENT => "Mozilla/5.0",
		CURLOPT_FILE => $fh,
		CURLOPT_HEADER => FALSE
		);
	curl_setopt_array($ch, $options);
	if (curl_exec($ch) === FALSE) {
		$error_msg = "Unable to retrieve $imgurl: ".curl_error($ch);
		error_log($error_msg);
		$messages[] = $error_msg;
	}
	fclose($fh);
	if (file_exists($fn)) {
		$ext = determine_extension($fn);
		$savefn = COMICDIR."/".preg_replace('/\s+/','_',$name).".".date("YMd",$date).".".$ext;
		rename($fn, APP_ROOT.$savefn);
		return $savefn;
	} else {
		return NULL;
	}
}

/**
 * Save the comic just fetched in the database
 *
 * @return id of comic just inserted
 * @author Tamara Temple
 **/
function save_comic($comic_id, $comic_date, $imgsrc, $filespec)
{
	global $db;
	$sql = "INSERT INTO `" . COMICSTBL . "` SET ";
	$insert_data[] = "`subscription_id`=" . $comic_id;
	$insert_data[] = "`imgsrc`='" . $imgsrc ."'";
	$insert_data[] = "`filespec`='" . $filespec . "'";
	$insert_data[] = "`comicdate`='" . date('Y-m-d H:i:s', $comic_date) . "'";
	$sql .= join(", ", $insert_data);
	debug("\$sql=$sql");
	$result = $db->query($sql);
	if ($result === FALSE)
		emit_fatal_error("Error inserting comic into database: \$sql=$sql error=" . $db->error);
	$last_id = $db->insert_id;
	return $last_id;
}


/*************************************************
 * MAIN
 *************************************************/

/**
 * Get the currently subscribed comics into an array
 */
$subscriptions = get_all_array(SUBSCRIPTIONSTBL);
$comic_ids = Array();
$start_time = microtime(TRUE);
$delays=0;

foreach ($subscriptions as $key => $subscription) {
	if ($key > 0) {
		sleep (DELAY_TIME);
		$delays++;
	}
	if (preg_match('/'.GOCOMICS.'/i', $subscription['uri'])) {
		list ($comic_date, $imgsrc) = parse_gocomics($subscription['uri']);
	}
	if (!empty($imgsrc)) {
		$lastcomic = get_last_comic_pulled($subscription['id']);
		if ($comic_date > strtotime($lastcomic['comicdate'])) {
			$filespec = pull_comic($subscription['name'],$comic_date,$imgsrc);
			$id = save_comic($subscription['id'], $comic_date, $imgsrc, $filespec);
			$comic_ids[] = $id;
		}
	}

	
}

$end_time = microtime(TRUE);
$elapsed_time = $end_time - $start_time;

$comics_retrieved = Array();
foreach ($comic_ids as $key) {
	$comic = get_this_comic($key);
	$comics_retrieved[] = $comic;
}
debug_var("\$comics_retrieved=",$comics_retrieved);

$smarty->assign('elapsed_time',$elapsed_time);
$smarty->assign('delays',$delays);
$smarty->assign('num_comics_retrieved',count($comics_retrieved));
$smarty->assign('comics_retrieved',$comics_retrieved);
$smarty->assign('title', "Retrieving Comics");
$smarty->display('newcomics.tpl');
