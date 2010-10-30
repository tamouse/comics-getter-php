<?xml version='1.0' encoding='UTF-8'?> 
<rss version='2.0' xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title>{$title}</title>
<link>{$link}</link>
<description>{$description}</description>
<language>{$language}</language>
<atom:link href="{$atomlink}" rel="self" type="application/rss+xml" />
{foreach from=$comics item=comic}
<item> 
<title>{$comic.name}</title>
<link>{$comic.uri}</link>
<description>{$comic.name} {$comic.comicdate}</description>
<content:encoded><![CDATA[<font size="+1"><b>{$comic.name}</b></font> <i>{$comic.comicdate}</i><br><img src="{$comic.fullurl}"> ]]></content:encoded>
<enclosure url="{$comic.fullurl}" length="{$comic.filesize}" type="{$comic.filetype}" />
<pubDate>{$comic.pubdate}</pubDate>
<guid>{$comic.fullurl}</guid>
</item>
{/foreach}
</channel></rss>
