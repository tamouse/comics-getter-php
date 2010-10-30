{if !empty($redirect_url)}
<div id="footer">
	Redirect to <a href="{$redirect_url}">{if !empty($redirect_target)}{$redirect_target}{else}{$redirect_url}{/if}</a>
</div>
{/if}
</body>


</html>
