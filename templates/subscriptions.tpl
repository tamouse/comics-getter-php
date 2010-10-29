{* display subscriptions to manage *}
{include file="header.tpl"}
{include file="nav.tpl"}

<div id="content">
	{include file="messages.tpl"}
	<form action="addsubscription.php" method="post" accept-charset="utf-8">
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
		<tr class="{cycle values="odd,even"}">
			<td>
			<a href="editsubscription.php?id={$subscription.id}" title="Edit Subscription"><img src="{$smarty.const.APP_URI_BASE}images/edit.jpg" width="32" height="32" alt="edit subscription"></a>
			<a href="deletesubscription.php?id={$subscription.id}" title="Delete Subscription"><img src="{$smarty.const.APP_URI_BASE}images/delete.jpg" width="32" height="32" alt="delete subscription"></a>
			</td>
			<td>
				{$subscription.name}
			</td>
			<td>{$subscription.uri}</td>
			<td>{$subscription.created|date_format:"%D %T"}</td>
			<td>{$subscription.updated|date_format:"%D %T"}</td>
		</tr>
		{/foreach}
	</table>
</div>
{include file="footer.tpl"}