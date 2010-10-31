{* show comics *}
{include file="header.tpl"}
{include file="nav.tpl"}

<div id="content">
	{include file="messages.tpl"}
	{include file="errors.tpl"}
	{if $num_comics > 0}
	<form action="deletecomics.php" method="get" accept-charset="utf-8">
		
		<table>
			{foreach from=$comics item=comic}
			<tr>
				<td class="checkbox"><input type="checkbox" name="comics[]" value="{$comic.id}" /></td>
				<td class="comicentry">
					<span class="comictitle">{$comic.name}</span>
					<span class="comicdate">{$comic.comicdate}</span>
					<br />
					<a href="{$comic.uri}"><img src="{$smarty.const.APP_URI_BASE}{$comic.filespec}" alt="{$comic.name} {$comic.comicdate}"  /> </a>
				</td>
			</tr>
			{/foreach}
		</table>
	
		{if !empty($additional_query_parms)}
			{foreach from=$additional_query_parms key=param item=value}
			<input type="hidden" name="{$param}" value="{$value}" />
			{/foreach}
		{/if}
		<p><input type="submit" value="Delete Checked Comics" /></p>
	</form>
	{else}
	<h3>No comics to display.</h3>
	{/if}
</div>
{include file="footer.tpl"}