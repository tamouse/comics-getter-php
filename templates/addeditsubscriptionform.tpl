{* form segment to add or edit a subscription *}
<form action="{$action}" method="post" accept-charset="utf-8" id="addeditform">
	{* first give hidden values for form *}
	<input type="hidden" name="comic_id" value="{$comic_id}" id="comic_id" />
	
	{* next the form elements *}
	<ul class="formelements">
	<li><label for="comic_name">Comic Name</label><input type="text" name="comic_name" value="{$comic_name}" id="comic_name" /></li>
	<li><label for="comic_uri">Comic URI</label><input type="text" name="comic_uri" value="{$comic_uri}" id="comic_uri" /></li>
	<li><input type="submit" value="{$action_type} Subscription" /></li>
	</ul>
</form>
