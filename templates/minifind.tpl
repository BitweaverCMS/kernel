{strip}
{form class="form-search minifind" method="get" action="`$smarty.server.SCRIPT_NAME`?`$hidden|@http_build_query`"}
	<div class="input-prepend form-inline">
		<input class="form-control input-sm search-query" type="text" name="find" placeholder="{$prompt|ucwords|escape}" value="{$find|default:$smarty.request.find|escape}" {if $prompt}onclick="if (this.value == '{$prompt}') this.value = '';"{/if}/>&nbsp;
		<button type="submit" class="btn btn-default btn-sm" name="search">{tr}Search{/tr}</button>
	</div>
{/form}
{/strip}
