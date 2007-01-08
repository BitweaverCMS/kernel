{strip}
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="generator" content="bitweaver - http://www.bitweaver.org" />
<meta name="description" content="{$gBitSystem->getConfig('site_description')}" />
<meta name="keywords" content="{$gBitSystem->getConfig('site_keywords')}" />

<link rel="shortcut icon" href="{$smarty.const.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
<link rel="icon" href="{$smarty.const.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />

{if $gBitSystem->isFeatureActive( 'site_header_extended_nav' )}
	<link rel="start" title="{$gBitSystem->getConfig('site_title')} {tr}Home{/tr}" href="{$smarty.const.BIT_ROOT_URL}" />

	{if $gBitSystem->isFeatureActive( 'site_header_help' )}
		<link rel="help" title="{tr}Help{/tr}" href="{$gBitSystem->getConfig('site_header_help')}" />
	{/if}
	{if $gBitSystem->isFeatureActive( 'site_header_copyright' )}
		<link rel="copyright" title="{tr}Copyright{/tr}" href="{$gBitSystem->getConfig('site_header_copyright')}" />
	{/if}
	{if $gBitSystem->isFeatureActive( 'site_header_contents' )}
		<link rel="contents" title="{tr}Contents{/tr}" href="{$gBitSystem->getConfig('site_header_contents')}" />
	{/if}
	{if $gBitSystem->isFeatureActive( 'site_header_glossary' )}
		<link rel="glossary" title="{tr}Glossary{/tr}" href="{$gBitSystem->getConfig('site_header_glossary')}" />
	{/if}

	{if $listInfo.current_page > 1}
		<link rel="first" title="{tr}First page{/tr}" href="{$pageUrl}&amp;list_page=1" />
		<link rel="previous" title="{tr}Previous page{/tr}" href="{$pageUrl}&amp;list_page={$listInfo.current_page-1}" />
	{/if}
	{if $listInfo.current_page < $listInfo.total_pages}
		<link rel="next" title="{tr}Next page{/tr}" href="{$pageUrl}&amp;list_page={$listInfo.current_page+1}" />
		<link rel="last" title="{tr}Last page{/tr}" href="{$pageUrl}&amp;list_page={$listInfo.total_pages}" />
	{/if}
{/if}

<script type="text/javascript">/* <![CDATA[ */
	var bitCookiePath = "{$smarty.const.BIT_ROOT_URL}";
	var bitCookieDomain = "";
	var bitIconDir = "{$smarty.const.LIBERTY_PKG_URL}icons/";
	var bitRootUrl = "{$smarty.const.BIT_ROOT_URL}";
/* ]]> */</script>

{* the order of the js files is crucial *}
<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/bitweaver.js"></script>
{if $loadAjax && !$loadDragDrop}
	{if $loadAjax == 'mochikit'}
		<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/libs/MochiKit/Base.js"></script>
		<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/libs/MochiKit/Async.js"></script>
		{foreach from=$mochikitLibs item=mLib}
			<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/libs/MochiKit/{$mLib}"></script>
		{/foreach}
	{else}
		<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/libs/prototype.js"></script>
		<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/ajax.js"></script>
		{if $loadDebug}
			<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/debugger.js"></script>
		{/if}
	{/if}
{/if}

{if $gBitSystem->isFeatureActive( 'feature_jscalendar' )}
	<link rel="stylesheet" title="{$style}" type="text/css" href="{$smarty.const.JSCALENDAR_PKG_URL}calendar-bitweaver.css" media="all" />
	<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}calendar.js"></script>
	<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}lang/calendar-en.js"></script>
	<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}calendar-setup.js"></script>
{/if}
{/strip}
