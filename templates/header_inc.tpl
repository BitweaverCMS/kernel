{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/header_inc.tpl,v 1.1.2.2 2005/07/15 12:01:15 squareing Exp $ *}
{strip}
	<meta name="generator" content="bitweaver - http://www.bitweaver.org" />
	<meta name="description" content="{$gBitSystemPrefs.site_description}" />
	<meta name="keywords" content="{$gBitSystemPrefs.site_keywords}" />

	<link rel="shortcut icon" href="{$gBitLoc.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
	<link rel="start" title="{$siteTitle} {tr}Home Page{/tr}" href="{$gBitLoc.BIT_ROOT_URL}" />
	<link rel="help" title="{tr}Help{/tr}" href="http://www.bitweaver.org/" />
{/strip}

<script type="text/javascript"><!--
	var tikiCookiePath = "{$gBitLoc.cookie_path}";
	var tikiCookieDomain = "{$gBitLoc.cookie_domain}";
	var tikiIconDir = "{$gBitLoc.LIBERTY_PKG_URL}icons";
	var tikiRootUrl = "{$gBitLoc.BIT_ROOT_URL}";
	//alert( tikiCookiePath );
	//alert( tikiCookieDomain );
--></script>
<script type="text/javascript" src="{$gBitLoc.KERNEL_PKG_URL}bitweaver.js"></script>

{include file="bitpackage:kernel/bidi.tpl"}

{* --- jscalendar block --- *}
{if $gBitSystem->isFeatureActive( 'feature_jscalendar' )}
	<link rel="StyleSheet" type="text/css" media="all" href="{$gBitLoc.JSCALENDAR_PKG_URL}calendar-system.css" title="system" />
	<script type="text/javascript" src="{$gBitLoc.JSCALENDAR_PKG_URL}calendar.js"></script>
	<script type="text/javascript" src="{$gBitLoc.JSCALENDAR_PKG_URL}lang/calendar-en.js"></script>
	<script type="text/javascript" src="{$gBitLoc.JSCALENDAR_PKG_URL}calendar-setup.js"></script>
{/if}

{$trl}