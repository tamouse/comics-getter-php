{* display subscriptions to manage *}
{include file="header.tpl"}
{include file="nav.tpl"}

<div id="content">
	{include file="messages.tpl"}
	{include file="errors.tpl"}
	<form action="addeditsubscription.php" method="get" accept-charset="utf-8">
		<input type="hidden" name="action" value="new" />
		{foreach from=$additional_query_parms key=param item=value}
		<input type="hidden" name="{$param}" value="{$value}" />
		{/foreach}
		<p><input type="submit" value="Add a new subscription" /></p>
	</form>
	<table>
		<tr><th>Actions</th>
			<th>Name</th>
			<th>URI</th>
			<th>Created</th>
			<th>Updated</th>
		</tr>
		{foreach from=$subscriptions item=subscription}
		<tr class="{cycle values="odd,even"}{if $subscription.id == $highlight_id}highlight{/if}">
			<td>
			<a href="addeditsubscription.php?action=edit&id={$subscription.id}{if !empty($additional_query_string)}&{$additional_query_string}{/if}" title="Edit Subscription"><img src="{$smarty.const.APP_URI_BASE}images/edit.jpg" width="32" height="32" alt="edit subscription"></a>
			<a href="deletesubscription.php?id={$subscription.id}{if !empty($additional_query_string)}&{$additional_query_string}{/if}" title="Delete Subscription"><img src="{$smarty.const.APP_URI_BASE}images/delete.jpg" width="32" height="32" alt="delete subscription"></a>
			</td>
			<td>
				{$subscription.name}
			</td>
			<td><a href="{$subscription.uri}">{$subscription.uri}</a></td>
			<td>{$subscription.created|date_format:"%D %T"}</td>
			<td>{$subscription.updated|date_format:"%D %T"}</td>
		</tr>
		{/foreach}
	</table>
</div>
{include file="footer.tpl"}