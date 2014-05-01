{strip}
{form class="form-search minifind" method="get" action="`$smarty.server.SCRIPT_NAME`?`$hidden|@http_build_query`"}
	<div class="input-append">
		<input class="input-small search-query" type="text" name="find" placeholder="{$prompt|escape}" value="{$find|default:$smarty.request.find|escape}" {if $prompt}onclick="if (this.value == '{$prompt}') this.value = '';"{/if}/>&nbsp;
		<button type="submit" class="btn" name="search">{tr}Search{/tr}</button>
	</div>
{/form}
{/strip}
