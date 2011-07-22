<?php

/**
 * Engines for parsing comics pages
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * @version $Id$
 * @copyright Tamara Temple Development, 28 October, 2010
 * @package comicgetter
 *
 * CHANGED: parse functions now fetch the contents using fetch_url_curl (functions.inc) instead of directly loading in the $html object
 *
 *
 **/

/**
 * Define DocBlock
 **/

require_once('simple_html_dom.php');  // NOTE: documentation is at http://simplehtmldom.sourceforge.net/manual.htm

/**
 * Parse a comic page at comics.com
 * 
 * CHANGED: comics.com now redirects to gocomics.com
 * @return whatever parse_gocomics gives us
 * @author Tamara Temple <tamara@tamaratemple.com>
 */
function parse_comics($uri)
{
	return parse_gocomics($url);
}

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
	if (empty($uri)) {
		$errors[]="Parameter uri not set on entry to parse_gocomics";
		return NULL;
	}
	$comicdate=NULL;
	$imgsrc=NULL;	
	// CHANGED: gocomics changed the way they present the comics on the comic's page. 
	debug(basename(__FILE__).'@'.__LINE__." enter parse_gocomics. uri=$uri");
	$html = new simple_html_dom();
	$contents = fetch_url_curl($uri);
	if (!$contents || empty($contents)) {
		$errors[]="No contents at $uri";
		return NULL;		
	}
	$html->load($contents);
	debug(basename(__FILE__).'@'.__LINE__." uri retrieved: ".htmlspecialchars(substr($html,0,50).'...'));
	$feature_item_container = $html->find('p.feature_item',0); // retrieve the comic reference
	debug(basename(__FILE__).'@'.__LINE__.' feature_item_container='.htmlspecialchars($feature_item_container));
	if (!$feature_item_container) {
		$errors[]="Could not find p.feature_item in $uri";
		return NULL;
	}
	$feature_item_img = $feature_item_container->find('img',0);
	debug(basename(__FILE__).'@'.__LINE__.' feature_item_img='.htmlspecialchars($feature_item_img));
	if (!$feature_item_img) {
		$errors[]="Could not find img in p.feature_item in $uri";
		return NULL;
	}
	$imgsrc=$feature_item_img->src;
	debug(basename(__FILE__).'@'.__LINE__.' imgsrc='.$imgsrc);
	if (!$imgsrc) {
		$errors[]="Could not find src attribute in img in p.feature_item in $uri";
		return NULL;
	}
	$feature_nav = $html->find('ul.feature-nav li',0); // retrieve the container for the date
	debug(basename(__FILE__).'@'.__LINE__.' feature_nav='.htmlspecialchars($feature_nav));
	if (!$feature_nav) {
		$errors[]="Could not find ul.feature-nav li in $uri";
		return NULL;
	}
	$comicdate_str = $feature_nav->innertext;
	debug(basename(__FILE__).'@'.__LINE__." comicdate_str=$comicdate_str");
	if (!$comicdate_str) {
		$errors[]="Could not find innertext in feature_nav in $uri";
		return NULL;
	}
	$comicdate = strtotime($comicdate_str);
	debug(basename(__FILE__)."@".__LINE__." returning Array($comicdate (".date(DATE_RFC822,$comicdate)."), $imgsrc)  ");
	
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
	$contents = fetch_url_curl($uri);
	if (!$contents || empty($contents)) {
		$errors[]="Could not retrieve contents of $uri via curl";
		return NULL;
	}
	$html = new simple_html_dom();
	$html->load($contents);
	$imgs = $html->find('img');
	$foundimg=FALSE;
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
			$foundimg=TRUE;
			break; /* we got a match, we're done */			
		}
	}
	if (!$foundimg) {
		$errors[]="No comic found in $url.";
	} else {
		//$messages[]="Found comic in $url. img=$imgsrc, date=".date(DATE_RFC822,$comicdate);
	}
	debug(basename(__FILE__).'@'.__LINE__." exiting parse_sinfest. comicdate=$comicdate (".date(DATE_RFC822,$comicdate)."). imgsrc=$imgsrc.");
	if (isset($comicdate) && isset($imgsrc)) return  array($comicdate, $imgsrc);
	return NULL;
}


/**
 * Parse the comic at http://twolumps.net
 *
 * @return array comicdate, imgsrc
 * @author Tamara Temple
 **/
function parse_twolumps($uri)
{
	global $errors, $messages;
	$contents = fetch_url_curl($uri);
	if (!$contents || empty($contents)) {
		$errors[] = "Could not retrieve contents of $uri via curl";
		return NULL;
	}
	
	$uri_parts = parse_url($uri);
	
	$html = new simple_html_dom();
	$html->load($contents);
	
	$imgs = $html->find('img');
	
	$foundimg = FALSE;
	
	foreach ($imgs as $img) {
		$src = $img->src;
		if (preg_match('@^/comics/(\d{4})(\d{2})(\d{2})\.jpg$@', $src, $matches)) {
			debug(basename(__FILE__).'@'.__LINE__." \$src=$src");
			debug(basename(__FILE__).'@'.__LINE__." matches=<pre>".print_r($matches,true)."</pre>");
			$imgsrc = $uri_parts['scheme'].'://'.$uri_parts['host'].$src;
			$year = $matches[1]; // saved search pattern from preg_match
			$month = $matches[2];
			$day = $matches[3];
			
			$comicdate=strtotime("$year/$month/$day");
			$foundimg=TRUE;
			break; // we got a match, we're done, break the foreach loop			
		}
	}
	
	if (!$foundimg) {
		$errors[]="No comic found in $uri";
		return NULL;
	} else {
		debug(basename(__FILE__).'@'.__LINE__." exiting ".__FUNCTION__." comicdate=(".date(DATE_RFC822,$comicdate).") imgsrc=($imgsrc)");
		return array($comicdate, $imgsrc);
	}
}