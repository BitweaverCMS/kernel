{strip}
{form class="minifind" legend="find in entries"}
	{foreach from=$hidden item=value key=name}
		<input type="hidden" name="{$name}" value="{$value}" />
	{/foreach}
	{biticon ipackage=liberty iname=find iexplain="Search"}
	<input type="text" name="find" value="{$find|escape}" />&nbsp;
	<input type="submit" name="search" value="{tr}Find{/tr}" />&nbsp;
	<input type="button" onclick="location.href='{$smarty.server.PHP_SELF}{if $hidden}?{/if}{foreach from=$hidden item=value key=name}{$name}={$value}&amp;{/foreach}'" value="{tr}Reset{/tr}" />
{/form}
{/strip}
