{* tempate for deletesubscription *}
{include file="header.tpl"}
{include file="nav.tpl"}

<div id="content">
	{include file="messages.tpl"}
	{include file="errors.tpl"}
	<table>
		<tr>
			<th>Name</th>
			<th>URI</th>
			<th>Created</th>
			<th>Updated</th>
		</tr>
		<tr class="{cycle values="odd,even"}">
			
			<td>
				{$subscription.name}
			</td>
			<td>{$subscription.uri}</td>
			<td>{$subscription.created|date_format:"%D %T"}</td>
			<td>{$subscription.updated|date_format:"%D %T"}</td>
		</tr>
	</table>
	<div id="confirm">
		<form id="confirmform" action="deletesubscription.php{if isset($additional_query_string)}?{$additional_query_string}{/if}" method="post" accept-charset="utf-8">
			<input type="hidden" name="id" value="{$subscription.id}" id="id" />
			<label for="confirm"><input type="checkbox" name="confirm" value="yes" id="confirm" />Confirm delete of subscription</label>
			<p><input type="submit" value="Submit" /></p>
		</form>		
	</div>
</div>
{include file="footer.tpl"}