{strip}
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="generator" content="bitweaver - http://www.bitweaver.org" />
<meta name="description" content="{$gBitSystem->getConfig('site_description')}" />
<meta name="keywords" content="{$gBitSystem->getConfig('site_keywords')}" />

<link rel="shortcut icon" href="{$smarty.const.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
<link rel="icon" href="{$smarty.const.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
<link rel="start" title="{$gBitSystem->getConfig('site_title')} {tr}Home Page{/tr}" href="{$smarty.const.BIT_ROOT_URL}" />
<link rel="help" title="{tr}Help{/tr}" href="http://www.bitweaver.org/" />

{if $gBitSystem->isPackageActive( 'rss' )}
	<link rel="rss feeds" title="{tr}RSS Syndication{/tr}" href="{$smarty.const.RSS_PKG_URL}" />
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
