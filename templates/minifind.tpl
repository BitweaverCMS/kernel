{strip}
{form class="minifind" legend="find in entries"}
	{foreach from=$hidden item=value key=name}
		<input type="hidden" name="{$name}" value="{$value}" />
	{/foreach}
	<input type="text" name="find" value="{$find|escape}" />&nbsp;
	<input type="submit" name="search" value="{tr}find{/tr}" />&nbsp;
	<a href="{$smarty.server.PHP_SELF}
		{if $hidden}?{/if}
		{foreach from=$hidden item=value key=name}
			{$name}={$value}&amp;
		{/foreach}
		">{tr}reset{/tr}</a>
{/form}
{/strip}
