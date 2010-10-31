<?php

/**
 * Delete comics - response to form
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


$errors = Array();
$messages = Array();

debug_var("\$_GET",$_GET);
debug_var("\$_POST:",$_POST);

if (empty($_POST)) {
	/***************************************************
	 * FIRST PASS -- Create confirm form for deletion
	 ***************************************************/
	if (!isset($_GET['comics']) || empty($_GET['comics'])) {
		$errors[] = "No comics set to delete";
		$redirect=buildredirect("index.php");
		debug("\$redirect=$redirect");
		if (!DEBUG) header("Location: $redirect"); else exit("<p><a href='$redirect'>Redirect</a></p>");
	}
	$comics=$_GET['comics'];
	$in = Array();
	foreach ($comics as $index => $comic_id) {
		if (!is_numeric($comic_id)) {
			$errors[] = "Non-numeric comic_id found: $comic_id";
		} else {
			$in[] = $comic_id;
		}
	}
	if (empty($in)) {
		$errors[] = "No valid comic_ids entered";
		$redirect=buildredirect("index.php");
		debug("\$redirect=$redirect");
		if (!DEBUG) header("Location: $redirect"); else exit("<p><a href='$redirect'>Redirect</a></p>");
	}
	/* we should have a good list of comic ids now */
	$sql = "SELECT ";
	$columns[] = 'c.id';
	$columns[] = 'c.subscription_id';
	$columns[] = 's.name';
	$columns[] = 's.uri';
	$columns[] = 'c.imgsrc';
	$columns[] = 'c.filespec';
	$columns[] = 'c.comicdate';
	$columns[] = 'c.pulltime';
	$sql .= join(", ",$columns);
	$sql .= " FROM ".COMICSTBL." as c, ".SUBSCRIPTIONSTBL." as s ";
	$whereparts[] = 'c.id in ('.join(",",$in).')'; /* the set of comics to gather */
	$whereparts[] = 'c.subscription_id=s.id';
	$sql .= " WHERE ".join(" AND ",$whereparts);
	
	debug("\$sql=$sql");

	$result=$db->query($sql);
	if ($result === FALSE) {
		$errors[] = "Getting selected comics failed: \$sql=$sql. Error=".$db->error;
		$redirect=buildredirect("index.php");
		debug("\$redirect=$redirect");
		if (!DEBUG) header("Location: $redirect"); else exit("<p><a href='$redirect'>Redirect</a></p>");
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
	if (empty($comics)) {
		$messages[] = "No comics found to be deleted.";
		$redirect=buildredirect("index.php");
		debug("\$redirect=$redirect");
		if (!DEBUG) header("Location: $redirect"); else exit("<p><a href='$redirect'>Redirect</a></p>");
	}
	

	$num_comics = count($comics);
	
	$smarty->assign('comics',$comics);
	$smarty->assign('num_comics',$num_comics);
	$smarty->assign('title',"Comics to delete");
	if (isset($messages)) $smarty->assign('messages',$messages);
	if (isset($errors)) $smarty->assign('errors',$errors);
	if (!empty($additional_query_parms)) $smarty->assign('additional_query_string',http_build_query($additional_query_parms));

	$smarty->display('deletecomics.tpl');

	
} else {
	/***********************************************
	 * SECOND PASS - processing confirm form reply
	 ***********************************************/
	if (isset($_POST['confirm']) && (!empty($_POST['confirm']) && 'yes' == $_POST['confirm'])) {
		/* deletion is confirmed! */
		if (isset($_POST['comics']) && (!empty($_POST['comics']))) {
			$comics = $_POST['comics'];
			$in = Array();
			foreach ($comics as $index => $comic_id) {
				if (is_numeric($comic_id)) {
					$in[] = $comic_id;
				} else {
					$errors[] = "Non-numeric comic_id found: $comic_id";
				}
			}
			if (empty($in)) {
				$errors[] = "No valid comic_ids entered";
			} else {
				/* should have a good set of comic ids in $in now */
				
				/**
				 * First -- delete the comic files from the file system
				 */
				$sql = "SELECT filespec FROM ".COMICSTBL." WHERE id IN (".join(",",$in).")";
				debug("\$sql=$sql");
				$result = $db->query($sql);
				if (!$result) emit_fatal_error("Query failed to obtain filespecs for comics to delete: \$sql=$sql. error=".$db->error);
				while ($row = $result->fetch_assoc()) {
					$filespec = APP_ROOT.$row['filespec'];
					if (file_exists($filespec)) {
						debug("Unlinking file $filespec");
						if (! unlink($filespec)) $errors[] = "Could not remove file $filespec"; else $messages[] = "$filespec removed";
					}
				}
				
				/**
				 * Second -- delete the comic entry in the database
				 */
				$sql = "DELETE FROM ".COMICSTBL." WHERE id IN (".join(",",$in).")";
				debug("\$sql=$sql");
				$result = $db->query($sql);
				if ($result) {
					$messages[] = "Comics successfully deleted";
				} else {
					$errors[] = "Comics could not be deleted. \$sql=$sql. error=".$db->error;
				}
			}
		} else {
			$errors[] = "No comics specified";
		}
	} else {
		$messages[] = "Deletion canceled";
	}
	$redirect=buildredirect("index.php");
	debug("\$redirect=$redirect");
    if (!DEBUG)	header("Location: $redirect"); else exit("<p><a href='$redirect'>Redirect</a></p>");
}