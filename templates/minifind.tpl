{strip}
{form class="form-search minifind" method="get" action="`$smarty.server.SCRIPT_NAME`?`$hidden|@http_build_query`"}
	<div class="input-prepend form-inline">
		<button type="submit" class="btn btn-default" name="search">{tr}Search{/tr}</button>
		<input class="form-control input-sm search-query" type="text" name="find" placeholder="{$prompt|escape}" value="{$find|default:$smarty.request.find|escape}" {if $prompt}onclick="if (this.value == '{$prompt}') this.value = '';"{/if}/>&nbsp;
	</div>
{/form}
{/strip}
