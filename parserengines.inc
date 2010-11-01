<?php

/**
 * Engines for parsing comics pages
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * @version $Id$
 * @copyright Tamara Temple Development, 28 October, 2010
 * @package comicgetter
 **/

/**
 * Define DocBlock
 **/

require_once('simple_html_dom.php');


/**
 * Parse the a comic page at gocomics.com
 *
 * @return Array(
 *		comicdate - timestamp of comic's date
 *		imgsrc - source url for comic's image
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function parse_gocomics($uri)
{
	global $errors;
	$html = new simple_html_dom();
	$html->load_file($uri);
	$feature = $html->find("div.feature",0);
	if (!isset($feature)) {
		$errors[] = "Can not find div.feature in html source $uri";
		return FALSE;
	}
	//debug_var("\$feature=",htmlentities($feature->outertext));
	$comicdate_element = $feature->find("li",0); /* comic date is first list item in feature div */
	if (!isset($comicdate_element)) {
		$errors[] = "Can not find comic date in html source $uri";
		return FALSE;
	}
	debug_var("\$comicdate_element=",htmlentities($comicdate_element));
	$comicdate = strtotime($comicdate_element->plaintext);
	debug("\$comicdate=$comicdate");
	$image_par = $feature->find("p.feature_item",0);
	if (!isset($image_par)) {
		$errors[] = "Can not find image paragraph in html source $uri";
		return FALSE;
	}
	debug_var("\$image_par=",htmlentities($image_par));
	$image_element = $image_par->find("a",0);
	if (!isset($image_element)) {
		$errors[] = "Can not find image element in html source $uri";
		if (NOHTML) {
			$errors[] = "\$image_par=$image_par";
		} else {
			$errors[] = "\$image_par=".htmlentities($image_par);
		}
		return FALSE;
	}
	debug_var("\$image_element=",htmlentities($image_element));
	if (isset($image_element->href)) {$imgsrc = $image_element->href;}
	else {
		$errors[] = "No image source in image element in html source $uri";
		if (NOHTML) {
			$errors[] = "\$image_element=".$image_element;
		} else {
			$errors[] = "\$image_element=".htmlentities($image_element);
		}
		return FALSE;
	}
	debug("\$imgsrc=$imgsrc");
	$image_element->clear();
	unset($image_element);
	$image_par->clear();
	unset($image_par);
	$comicdate_element->clear();
	unset($comicdate_element);
	$feature->clear();
	unset($feature);
	$html->clear();
	unset($html);
	return Array($comicdate, $imgsrc);
}



/**
 * Parse the comic page at sinfest.net
 *
 * @return array($comicdate, $imgsrc)
 * @author Tamara Temple
 **/
function parse_sinfest($uri)
{
	global $errors, $messages;
	
	$html = new simple_html_dom();
	$html->load_file($uri);
	$imgs = $html->find('img');
	foreach ($imgs as $img) {
		$src=$img->src;
		if (preg_match('/comikaze/',$src)) {
			// this is the image source
			debug("\$src=$src");
			$imgsrc=$src;
			$srcparts = parse_url($src);
			$filename=basename($srcparts['path'],'.gif');
			debug("\$filename=$filename");
			$comicdate = strtotime($filename);
			debug("\$comicdate=$comicdate");
			break; /* we got a match, we're done */			
		}
	}
	if (isset($comicdate) && isset($imgsrc)) return  array($comicdate, $imgsrc);
	return NULL;
}