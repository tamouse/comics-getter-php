{* index file for application *}
{include file="header.tpl"}
{include file="nav.tpl"}

<div id="content">
	{include file="messages.tpl"}
	{include file="errors.tpl"}
	{if $num_comics > 0}
	<ul>{foreach from=$comics item=comic}
		<li class="comicentry">
			<span class="comictitle">{$comic.name}</span>
			<span class="comicdate">{$comic.comicdate}</span>
			<br />
			<a href="{$comic.uri}"><img src="{$smarty.const.APP_URI_BASE}{$comic.filespec}" alt="{$comic.name} {$comic.comicdate}"  /> </a>
		</li>
	{/foreach}</ul>
	{else}
	<h3>No comics to display.</h3>
	{/if}
</div>

{include file="footer.tpl"}