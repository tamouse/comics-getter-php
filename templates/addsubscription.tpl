{* add a new comic subscription *}
{include file="header.tpl" title="Add a new comic subscription"}
{include file="nav.tpl"}

{include file="addeditsubscriptionform.tpl" action="addsubscription.php" action_type="add" $comic_id=0 $comic_name='' $comic_uri=''}

{include file="footer.tpl"}