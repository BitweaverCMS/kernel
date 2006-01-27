{strip}
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="generator" content="bitweaver - http://www.bitweaver.org" />
	<meta name="description" content="{$gBitSystemPrefs.site_description}" />
	<meta name="keywords" content="{$gBitSystemPrefs.site_keywords}" />

	<link rel="shortcut icon" href="{$smarty.const.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
	<link rel="icon" href="{$smarty.const.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
	<link rel="start" title="{$gBitSystemPrefs.siteTitle} {tr}Home Page{/tr}" href="{$smarty.const.BIT_ROOT_URL}" />
	<link rel="help" title="{tr}Help{/tr}" href="http://www.bitweaver.org/" />
{/strip}
<script type="text/javascript">//<![CDATA[
	{if $gBitSystem->isFeatureActive( 'rememberme' )}
		var bitCookiePath = "{$gBitSystem->mPrefs.cookie_path}";
		var bitCookieDomain = "{$gBitSystem->mPrefs.cookie_domain|default:"`$smarty.server.SERVER_NAME`"}";
	{else}
		var bitCookiePath = "{$smarty.const.BIT_ROOT_URL}";
		var bitCookieDomain = "";
	{/if}
	var bitIconDir = "{$smarty.const.LIBERTY_PKG_URL}icons/";
	var bitRootUrl = "{$smarty.const.BIT_ROOT_URL}";
//]]></script>
{* the order of the js files is crucial *}
<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/bitweaver.js"></script>
{if $loadAjax}
	{if $jsDebug}
		<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/libs/prototype_1.4.js"></script>
		<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/debugger.js"></script>
	{else}
		<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/libs/prototype.js"></script>
	{/if}
	<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/ajax.js"></script>
{/if}
{strip}
	{include file="bitpackage:kernel/bidi.tpl"}

	{* --- jscalendar block --- *}
	{if $gBitSystem->isFeatureActive( 'feature_jscalendar' )}
		<link rel="stylesheet" title="{$style}" type="text/css" href="{$smarty.const.JSCALENDAR_PKG_URL}calendar-bitweaver.css" media="all" />
		<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}calendar.js"></script>
		<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}lang/calendar-en.js"></script>
		<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}calendar-setup.js"></script>
	{/if}

	{$trl}
{/strip}
