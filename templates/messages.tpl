{* display messages from application *}
<div id="messages">
	<ul>
	{foreach from=$messages item=message}
		<li class="message">{$message}</li>
	{/foreach}
	</ul>
</div>