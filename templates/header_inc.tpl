{strip}
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="generator" content="bitweaver - http://www.bitweaver.org" />
	<meta name="description" content="{$gBitSystemPrefs.site_description}" />
	<meta name="keywords" content="{$gBitSystemPrefs.site_keywords}" />

	<link rel="shortcut icon" href="{$smarty.const.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
	<link rel="icon" href="{$smarty.const.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
	<link rel="start" title="{$siteTitle} {tr}Home Page{/tr}" href="{$smarty.const.BIT_ROOT_URL}" />
	<link rel="help" title="{tr}Help{/tr}" href="http://www.bitweaver.org/" />
{/strip}
<script type="text/javascript">//<![CDATA[
	{if $gBitSystem->isFeatureActive( 'rememberme' )}
		var bitCookiePath = "{$gBitSystem->mPrefs.cookie_path}";
		var bitCookieDomain = ".{$gBitSystem->mPrefs.cookie_domain}";
	{else}
		var bitCookiePath = "{$smarty.const.BIT_ROOT_URL}";
		var bitCookieDomain = "";
	{/if}
	var bitIconDir = "{$smarty.const.LIBERTY_PKG_URL}icons/";
	var bitRootUrl = "{$smarty.const.BIT_ROOT_URL}";
//]]></script>
<script type="text/javascript" src="{$smarty.const.THEMES_PKG_URL}js/bitweaver.js"></script>
<script type="text/javascript" src="{$smarty.const.THEMES_PKG_URL}js/ajax.js"></script>
{strip}
	{include file="bitpackage:kernel/bidi.tpl"}

	{* --- jscalendar block --- *}
	{if $gBitSystem->isFeatureActive( 'feature_jscalendar' )}
		<link rel="stylesheet" title="{$style}" type="text/css" href="{$smarty.const.JSCALENDAR_PKG_URL}calendar-system.css" media="all" />
		<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}calendar.js"></script>
		<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}lang/calendar-en.js"></script>
		<script type="text/javascript" src="{$smarty.const.JSCALENDAR_PKG_URL}calendar-setup.js"></script>
	{/if}

	{$trl}
{/strip}
