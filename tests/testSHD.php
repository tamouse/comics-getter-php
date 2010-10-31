<?php
header("Content-type: text/plain");

ini_set('display_errors',TRUE);
ini_set('display_startup_errors',TRUE);

require_once('simple_html_dom.php');



$html = new simple_html_dom();

$html->load("<html><body><div class=\"content\"><img src=\"http://localhost/~tamara/comicgetter/comics/Show.2010Oct29.gif\"><p class=\"feature\">Featured item</p></div></body></html>");
echo $html . "\n";
$contentdiv = $html->find("div.content",0);
echo $contentdiv . "\n";
$img = $contentdiv->find("img",0);
echo $img . "\n";
$src = $img->src;
echo $src . "\n";
$html->clear();
unset($html, $contentdiv, $img, $src);

$html = new simple_html_dom();
$html->load("<html><body><div class=\"content\"><img><p class=\"feature\">Featured item</p></body></html>");
echo $html . "\n";
$contentdiv=$html->find("div.content",0);
echo $contentdiv . "\n";
$img=$contentdiv->find("img",0);
echo $img . "\n";
if (isset($img->src)) $src=$img->src;
echo (isset($src) ? $src : "\$src not set") . "\n";
$html->clear();
unset($html, $contentdiv, $img, $src);

$html = new simple_html_dom();
$html->load("<html><body><div class=\"notcontent\"><img><p class=\"feature\">Featured item</p></body></html>");
echo $html . "\n";
$contentdiv=$html->find("div.content",0);
echo (isset($contentdiv) ? $contentdiv : "\$contentdiv not set") . "\n";
$html->clear();
unset($html, $contentdiv);

$html = new simple_html_dom();
$html->load("<p class=\"feature_item\"><a href = 'http://imgsrv.gocomics.com/dim/?fh=0e427d412313afddc55e67e318263824&w=750'>
          <img 
          src='http://imgsrv.gocomics.com/dim/?fh=0e427d412313afddc55e67e318263824&w=600'
           width='600' 
          height='398'
           alt='Liberty Meadows'>
        </a><!-- end of first case feature_item --> </p>
");
echo "\$html=".$html . "\n";
$img = $html->find("img",0);
echo "\$img=".(isset($img)?$img:"not set") . "\n";
if (isset($img)) $src = $img->src;
echo "\$src=".(isset($src)?$src:"not set") . "\n";
if (isset($img)) $img->clear();
$html->clear();
unset ($html, $img);


echo "Done.\n";

