<?php

/**
 * Functions for parsing comics page at gocomics.com
 *
 * @author Tamara Temple
 * @version $Id$
 * @copyright Tamara Temple Development, 28 October, 2010
 * @package default
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
 * @author Tamara Temple
 **/
function parse_gocomics($uri)
{
	$html = new simple_html_dom();
	$html->load_file($uri);
	$feature = $html->find("div.feature",0);
	debug_var("\$feature=",htmlentities($feature->outertext));
	$comicdate_element = $feature->find("li",0); /* comic date is first list item in feature div */
	debug_var("\$comicdate_element=",htmlentities($comicdate_element));
	$comicdate = strtotime($comicdate_element->plaintext);
	debug("\$comicdate=$comicdate");
	$image_par = $feature->find("p.feature_item",0);
	debug_var("\$image_par=",htmlentities($image_par));
	$image_element = $image_par->find("img",0);
	debug_var("\$image_element=",htmlentities($image_element));
	$imgsrc = $image_element->src;
	debug("\$imgsrc=$imgsrc");
	$feature->clear();
	unset($feature);
	$html->clear();
	unset($html);
	return Array($comicdate, $imgsrc);
}