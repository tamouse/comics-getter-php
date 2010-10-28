{* index file for application *}
{include file="header.tpl"}
{include file="nav.tpl"}

<div id="content">
	<h2>Latest Comics</h2>
	<ul>
		<li>
			<p class="comic">
				<span class="comictitle">{$comic['title']}</span>
				<span class="comicdate">{$comic['comicdate']}</span>
				<br />
				<a href="{$comic['comicuri']}"><img src="{$comicuri}" alt="{$title} {$comicdate}"  /> </a>
			</p>
		</li>
	</ul>
</div>

{include file="footer.tpl"}
