{* add a new comic subscription *}
{include file="header.tpl" title="Add a new comic subscription"}
{include file="nav.tpl"}

<div id="content">
{include file="messages.tpl"}
{include file="errors.tpl"}
{* form segment to add or edit a subscription *}
<form action="{$action}" method="post" accept-charset="utf-8" id="addeditform">
	{* first give hidden values for form *}
	<input type="hidden" name="subscription_id" value="{$subscription_id}" id="comic_id" />
	
	{* next the form elements *}
	<ul class="formelements">
	<li><label for="comic_name">Comic Name</label><input type="text" name="comic_name" value="{$comic_name}" id="comic_name" /></li>
	<li><label for="comic_uri">Comic URI</label><input type="text" name="comic_uri" value="{$comic_uri}" id="comic_uri" /></li>
	<li><input type="submit" value="{$action_type} Subscription" /></li>
	</ul>
</form>
</div>

{include file="footer.tpl"}