{* add a new comic subscription *}
{include file="header.tpl"}
{include file="nav.tpl"}

<div id="content">
{include file="messages.tpl"}
{include file="errors.tpl"}
{* form segment to add or edit a subscription *}
<form action="{$action}{if !empty($additional_query_string)}?{$additional_query_string}{/if}" method="post" accept-charset="utf-8" id="addeditform">
	{* first give hidden values for form *}
	<input type="hidden" name="subscription_id" value="{$subscription_id}"  />
	<input type="hidden" name="action_type" value="{$action_type}" />
	
	{* next the form elements *}
	<ul class="formelements">
	<li><label for="comic_name">Comic Name</label><input type="text" name="comic_name" value="{$comic_name}"  /></li>
	<li><label for="comic_uri">Comic URI</label><input type="text" name="comic_uri" value="{$comic_uri}"  /></li>
	<li><input type="submit" value="{$action_type} Subscription" /></li>
	</ul>
</form>
</div>

{include file="footer.tpl"}