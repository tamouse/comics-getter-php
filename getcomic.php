<?php
/**
 * Get comics from service
 *
 * Different comic web services have different layouts, so must be parsed differenlty.
 * Based on the comic's URI, the service will be determined and the appropriate routine to parse
 * the returned HTML will be called, rendering the current comic's image source URI.
 * The image will be retrieved and stored locally, with the stored image's file path saved in the data base.
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * @version $Id$
 * @copyright Tamara Temple Development, 28 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

require_once('config.inc');
require_once('parserengines.inc');

/*********************************************************
 * FUNCTIONS
 *********************************************************/

/**
 * Determine the extension of the file by checking it's mimetype
 *
 * @return ext - string
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function determine_extension($fn)
{
	$cmd = MIMETYPE." ".escapeshellcmd($fn)." 2>/dev/null";
	debug("\$cmd=$cmd");
	$result = rtrim(`$cmd`);
	debug("\$result=$result");
	switch ($result) {
		case 'image/jpeg':
			$ext = "jpg";
			break;
		
		case 'image/gif':
			$ext = "gif";
			break;
			
		case 'image/png':
			$ext = "png";
			break;
			
		default:
			$ext = "dat";
			break;
	}
	return $ext;
}



/**
 * Pull the comic image and save locally
 *
 * @return file_spec - APP_ROOT relative path to file retrieved
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function pull_comic($name, $date, $imguri)
{
	global $messages,$errors;
	$ch = curl_init();
	$fn = tempnam(TEMPDIR, "comic");
	debug("\$fn=$fn");
	$fh = fopen($fn,'w');
	$options = Array(
		CURLOPT_URL => $imguri,
		CURLOPT_USERAGENT => "Mozilla/5.0",
		CURLOPT_FILE => $fh,
		CURLOPT_HEADER => FALSE,
		CURLOPT_FOLLOWLOCATION => TRUE,
		CURLOPT_MAXREDIRS => '10'
	);
	curl_setopt_array($ch, $options);
	if (curl_exec($ch) === FALSE) {
		$error_msg = "Unable to retrieve $imgurl: ".curl_error($ch);
		error_log($error_msg);
		$errors[] = $error_msg;
	}
	fclose($fh);
	curl_close($ch);
	if (file_exists($fn)) {
		chmod($fn,0666);
		$ext = determine_extension($fn);
		debug("\$ext=$ext");
		if ($ext == 'dat') {
			$error_msg = "File retrieved $fn is not an image type of file";
			$errors[] = $error_msg;
			error_log($error_msg);
			return NULL;
		}
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
 * @author Tamara Temple <tamara@tamaratemple.com>
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
$messages = Array();
$errors = Array();

foreach ($subscriptions as $key => $subscription) {
	if ($key > 0) {
		sleep (DELAY_TIME);
		$delays++;
	}
	debug_var("\$subscription:",$subscription);
	$engine = get_parse_engine($subscription['uri']);
	if (isset($engine)) {
		list ($comic_date, $imgsrc) = $engine($subscription['uri']);
		if (!empty($imgsrc)) {
			$lastcomic = get_last_comic_pulled($subscription['id']);
			if ($comic_date > strtotime($lastcomic['comicdate'])) {
				$filespec = pull_comic($subscription['name'],$comic_date,$imgsrc);
				if (isset($filespec)) {
					$id = save_comic($subscription['id'], $comic_date, $imgsrc, $filespec);
					$comic_ids[] = $id;
				} else {
					$errors[] = "No comic image retrieved from ".$imgsrc;
				}
			}
		} else {
			$errors[] = "No comic image in page at ".$subscription['uri'];
		}
	} else {
		$errors[] = "No parse engine for ".$subscription['uri'];
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

if (NOHTML) {
	if (!empty($messages)) echo "Messages:\n" . join("\n",$messages) . "\n";
	if (!empty($errors)) echo "Errors:\n" . join("\n",$errors) . "\n";
	if (empty($comics_retrieved)) {
		echo "No comics retrieved this pass\n";
	} else {
		echo count($comics_retrieved) . " comic(s) retrieved this pass\n";
	}
	printf("Elapsed time: %.4f with %d delay(s)\n",$elapsed_time,$delays);
	exit;
}

$smarty->assign('additional_query_string',http_build_query($additional_query_parms));
if (!empty($messages)) $smarty->assign('messages',$messages);
if (!empty($errors)) $smarty->assign('errors',$errors);
$smarty->assign('elapsed_time',$elapsed_time);
$smarty->assign('delays',$delays);
$smarty->assign('num_comics_retrieved',count($comics_retrieved));
$smarty->assign('comics_retrieved',$comics_retrieved);
$smarty->assign('title', "Retrieving Comics");
$smarty->display('newcomics.tpl');
