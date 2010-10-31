{* Display comics to delete, confirm deletion *}
{include file="header.tpl"}
{include file="nav.tpl"}
<div id="content">
	{include file="messages.tpl"}
	{include file="errors.tpl"}
	<h3>The following comics are scheduled to be deleted:</h3>
	<form action="deletecomics.php{if isset($additional_query_string)}?{$additional_query_string}{/if}" method="post">
	<table>
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>Comic Date</th>
			<th>File Spec</th>
			<th>Date Pulled</th>
		</tr>
		{foreach from=$comics item=comic}
		<tr>
			<td>{$comic.id}<input type="hidden" name="comics[]" value="{$comic.id}" /></td>
			<td>{$comic.name}</td>
			<td>{$comic.comicdate}</td>
			<td>{$comic.filespec}</td>
			<td>{$comic.pulltime|date_format:"%D-%T"}</td>
		</tr>
		{/foreach}
	</table>
	<div id="confirm">
		<label for="confirm"><input type="checkbox" name="confirm" value="yes" />Confirm deletion of the above comics.</label>
		
		<p><input type="submit" value="Proceed with delete" /></p>

	</div>
	</form>
</div>
{include file="footer.tpl"}