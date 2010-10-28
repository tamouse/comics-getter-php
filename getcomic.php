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


/**
 * Get the currently subscribed comics into an array
 */
$subscriptions = get_all_array(SUBSCRIPTIONSTBL);
$comics_retrieved = Array();

foreach ($subscriptions as $key => $comic) {
	$comic_page = file_get_contents($comic['uri']);
	if (pcreg_match(GOCOMICS, $comic['uri'])) {
		($comic_date, $imgsrc) = parse_gocomics($comic_page);
	}
	if (!empty($imgsrc)) {
		$lastcomic = get_last_comic_pulled($comic['id']);
		if (strtodate($comic_date) > $lastcomic) {
			($filespec, $pulltime) = pull_comic($imgsrc);
			$id = save_comic($comic['id'], $comic_date, $imgsrc, $filespec, $pulltime);
			$comics_retrieved[] = $id;
		}
	}
	
}

/**
 * Get the last comic pulled for a given subscription
 *
 * @return date of last comic pulled
 * @author Tamara Temple
 **/
function get_last_comic_pulled($comic_id)
{
	$sql = "SELECT comicdate FROM " . COMICSTBL . " WHERE subscription_id=" . $comic_id . " LIMIT 1";
	$result = $db->query($sql);
	if ($result === FALSE) 
		emit_fatal_error("Error selecting comicdate from " . COMICSTBL . ": \$sql=$sql ; error: " . $db->error);
	if ($result->num_rows > 0) {
		$lastcomicdate = $result->fetch_field();
		$lastcomic = strtotime($lastcomicdate);
	} else {
		$lastcomic = -1;
	}
	$result->free();
	return $lastcomic;
}

/**
 * Save the comic just fetched in the database
 *
 * @return id of comic just inserted
 * @author Tamara Temple
 **/
function save_comic($comic_id, $comic_date, $imgsrc, $filespec, $pulltime)
{
	$sql = "INSERT INTO `" . COMICSTBL . "` SET ";
	$insert_data[] = "`subscription_id`=" . $comic_id;
	$insert_data[] = "`imgsrc`='" . $imgsrc ."'";
	$insert_data[] = "`filespec`='" . $filespec . "'";
	$insert_data[] = "`comicdate`='" . $comic_date . "'";
	$insert_data[] = "`pulltime`=" . $pulltime;
	$sql .= join(", ", $insert_data);
	debug("\$sql=$sql");
	}
	$result = $db->query($sql);
	if ($result === FALSE)
		emit_fatal_error("Error inserting comic into database: \$sql=$sql error=" . $db->error);
	$last_id = $db->insert_id;
	return $last_id;
}