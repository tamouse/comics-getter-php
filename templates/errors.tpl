{* print error messages *}
<div id="errors">
	<ul>
		{foreach from=$errors item=error}
		<li class="error">{$error}</li>
		{/foreach}
	</ul>
</div>