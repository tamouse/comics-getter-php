{* display list of new comics retrieved *}
{include file="header.tpl"}
{include file="nav.tpl"}

<div id="content">
	{include file="messages.tpl"}
	{include file="errors.tpl"}
	{if $num_comics_retrieved > 0}
	<table>
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>Date</th>
			<th>Img URI</th>
			<th>File Spec</th>
			<th>Pull Date</th>
		</tr>
		{foreach from=$comics_retrieved item=comic}
		<tr class="{cycle values="odd,even"}">
			<td>{$comic.id}</td>
			<td>{$comic.name}</td>
			<td>{$comic.comicdate}</td>
			<td>{$comic.imgsrc}</td>
			<td>{$comic.filespec}</td>
			<td>{$comic.pulltime|date_format:"%D %T"}</td>
		</tr>
		{/foreach}
	</table>
	{else}
	<h3>No comics retrieved this trip.</h3>
	{/if}
	<p>Elapsed time: {$elapsed_time|string_format:"%.4f"} seconds with {$delays} delay{if $delays>1}s{/if}.</p>
</div>
{include file="footer.tpl"}