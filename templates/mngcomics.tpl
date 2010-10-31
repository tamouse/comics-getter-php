{* mngcomics
 *  Created by Tamara Temple <tamouse@gmail.com> on 2010-10-31.
 *  Copyright (c) 2010 Tamara Temple Development. All rights reserved.
 *}
{include file="header.tpl"}
{include file="nav.tpl"}
<div id="content">
	{include file="messages.tpl"}
	{include file="errors.tpl"}
	<form action="deletecomics.php" method="get" accept-charset="utf-8">
		{foreach from=$comiclist key=name item=comics}
			<h3>{$name}</h3>
			<table>
				<tr>
					<th width="1%">Select</th>
					<th width="15%">Comic Date</th>
					<th width="50%">File Spec</th>
					<th width="30%">Pull Time</th>
				</tr>
				{foreach from=$comics item=comic}
				<tr>
					<td><input type="checkbox" name="comics[]" value="{$comic.id}" /></td>
					<td>{$comic.comicdate}</td>
					<td>{$comic.filespec}</td>
					<td>{$comic.pulltime|date_format:"%D-%T"}</td>
				</tr>
				{/foreach}
			</table>
			<br />
		{/foreach}


		{foreach from=$additional_query_parms key=param item=value}
			<input type="hidden" name="{$param}" value="{$value}" />
		{/foreach}
		<p><input type="submit" value="Delete checked comics" /></p>
	</form>
</div>
{include file="footer.tpl"}


