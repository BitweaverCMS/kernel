{* $Heade: /cvsoot/tikipo/_p_tp_themes/styles/forget/altenative/pint_peview.css,v 1.3 2005/04/10 20:37:29 squaeing Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
{strip}
	<title>{$browserTitle} - {$siteTitle}</title>
	<meta name="generator" content="bitweaver - http://www.bitweaver.org" />
	<meta name="description" content="{$gBitSystemPrefs.site_description}" />
	<meta name="keywords" content="{$gBitSystemPrefs.site_keywords}" />

	{* link rel block *}
	<link rel="shortcut icon" href="{$gBitLoc.BIT_ROOT_URL}favicon.ico" type="image/x-icon" />
	<link rel="start" title="{$siteTitle} {tr}Home Page{/tr}" href="{$gBitLoc.BIT_ROOT_URL}" />
	<link rel="help" title="{tr}Help{/tr}" href="http://www.bitweaver.org/" />
	{if $structureInfo}
		<link rel="index" title="{tr}Contents{/tr}" href="index.php?structure_id={$structureInfo.root_structure_id}" />
		{if $structureInfo.parent.structure_id}
			<link rel="up" title="{tr}Up{/tr}" href="index.php?structure_id={$structureInfo.parent.structure_id}" />
		{/if}
		{if $structureInfo.prev.structure_id}
			<link rel="prev" title="{tr}Previous{/tr}" href="index.php?structure_id={$structureInfo.prev.structure_id}" />
		{/if}
		{if $structureInfo.next.structure_id}
			<link rel="next" title="{tr}Next{/tr}" href="index.php?structure_id={$structureInfo.next.structure_id}" />
		{/if}
	{/if}

	{if $gGallery->mInfo.previous_image_id}
		<link rel="prev" title="{tr}Previous{/tr}" href="{$gContent->getDisplayUrl($gGallery->mInfo.previous_image_id)|escape}" />
	{/if}
	{if $gGallery->mInfo.next_image_id}
		<link rel="next" title="{tr}Next{/tr}" href="{$gContent->getDisplayUrl($gGallery->mInfo.next_image_id)|escape}" />
	{/if}

	{if $gBitSystem->isPackageActive( 'rss' )}
		{if $gBitLoc.ACTIVE_PACKAGE eq 'blogs' and $gBitUser->hasPermission( 'bit_p_read_blog' )}
			<link rel="alternate" type="application/rss+xml" title="{$title}{$post_info.blogtitle}" href="{$gBitLoc.BLOGS_PKG_URL}blogs_rss.php?blog_id={$blog_id}" />
		{/if}
		{if $gBitLoc.ACTIVE_PACKAGE eq 'wiki' and $gBitUser->hasPermission( 'bit_p_view' )}
			<link rel="alternate" type="application/rss+xml" title="{$siteTitle} - wiki" href="{$gBitLoc.RSS_PKG_URL}wiki_rss.php" />
		{/if}
	{/if}

	{* stylesheets *}
	{if $gBitLoc.styleSheet}
		<link rel="stylesheet" title="{$style}" type="text/css" href="{$gBitLoc.styleSheet}" media="all" />
	{/if}
	{if $gBitLoc.browserStyleSheet}
		<link rel="stylesheet" title="{$style}" type="text/css" href="{$gBitLoc.browserStyleSheet}" media="all" />
	{/if}
	{if $gBitLoc.customStyleSheet}
		<link rel="stylesheet" title="{$style}" type="text/css" href="{$gBitLoc.custumStyleSheet}" media="all" />
	{/if}
	{foreach from=$gBitLoc.altStyleSheets item=alt_path key=alt_name}
		<link rel="alternate stylesheet" title="{$alt_name}" type="text/css" href="{$alt_path}" media="screen" />
	{/foreach}
{/strip}

	{* tikipro javascript block *}
	<script type="text/javascript"><!--
		var tikiCookiePath = "{$gBitLoc.cookie_path}";
		var tikiCookieDomain = "{$gBitLoc.cookie_domain}";
		var tikiIconDir = "{$gBitLoc.LIBERTY_PKG_URL}icons";
		var tikiRootUrl = "{$gBitLoc.BIT_ROOT_URL}";
		//alert( tikiCookiePath );
		//alert( tikiCookieDomain );
	--></script>
	<script type="text/javascript" src="{$gBitLoc.KERNEL_PKG_URL}bitweaver.js"></script>

	{if $gBitSystemPrefs.disable_jstabs ne 'y'}
		<script type="text/javascript" src="{$gBitLoc.THEMES_PKG_URL}js/tabs/tabpane.js"></script>
	{/if}

	{if $gBitLoc.browser.client eq 'ie'}
		<!-- this wierdness fixes png display and CSS driven dropdown menus in GUESS WHAT BROWSER -->
		<!--[if gte IE 5.5000]>
			<script type="text/javascript" src="{$gBitLoc.THEMES_PKG_URL}js/pngfix.js"></script>
		<![endif]-->
		<!--[if gte IE 5.0]>
			<script type="text/javascript" src="{$gBitLoc.KERNEL_PKG_URL}bitweaver.js"></script>
				<script type="text/javascript">
					var nexusMenus = new Array(1)
					nexusMenus[0] = 'nav'
				{php}if( is_file( TEMP_PKG_PATH.'nexus/modules/hoverfix_array.js' ) ) { include_once( TEMP_PKG_PATH.'nexus/modules/hoverfix_array.js' ); }{/php}
			</script>
			<script type="text/javascript" src="{$gBitLoc.THEMES_PKG_URL}js/hoverfix.js"></script>
		<![endif]-->
	{/if}

	{* wysiwyg editor tinymce *}
	{if $gBitSystem->isPackageActive( 'tinymce' ) and ( $gBitLoc.browser.client eq 'mz' or $gBitLoc.browser.client eq 'ie' ) and $gBitLoc.ACTIVE_PACKAGE ne 'phpbb'}
		<script type="text/javascript" src="{$gBitLoc.TINYMCE_PKG_URL}jscripts/tiny_mce.js"></script>
		<script type="text/javascript">
			//<![CDATA[
			tinyMCE.init({literal}{{/literal}
				mode		: "exact",
				elements	: "{$textarea_id}",
				{if $gBitSystemPrefs.tinymce_ask eq 'y'}
					ask		: true,
				{/if}
				theme		: "advanced",
				plugins		: "table",
				debug		: false,
				content_css : "{$gBitLoc.THEMES_STYLE_URL}tinymce/tinymce.css",
				theme_advanced_buttons3_add_before : "tablecontrols,separator",
				theme_advanced_styles : "Tiki Box=tikibox;Tiki Bar=tikibar;Tiki Table=tikitable;Odd table row=odd;Even table row=even"
			{literal}}{/literal});
			//]]>
		</script>
	{/if}

	{include file="bitpackage:kernel/bidi.tpl"}

	{* --- jscalendar block --- *}
	{if $gBitSystemPrefs.feature_jscalendar eq 'y'}
		<link rel="StyleSheet" type="text/css" media="all" href="{$gBitLoc.JSCALENDAR_PKG_URL}calendar-system.css" title="system" />
		<script type="text/javascript" src="{$gBitLoc.JSCALENDAR_PKG_URL}calendar.js"></script>
		<script type="text/javascript" src="{$gBitLoc.JSCALENDAR_PKG_URL}lang/calendar-en.js"></script>
		<script type="text/javascript" src="{$gBitLoc.JSCALENDAR_PKG_URL}calendar-setup.js"></script>
	{/if}

	{$trl}
</head>
<body>
{if $minical_reminders>100}
	<iframe width="0" height="0" border="0" src="{$gBitLoc.CALENDAR_PKG_URL}minical_reminders.php" />
{/if}
{if $gBitSystemPrefs.feature_helppopup eq 'y'}
	{popup_init src="`$gBitLoc.THEMES_PKG_URL`js/overlib.js"}
{/if}
