<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$bitlanguage|default:'en'}" lang="{$bitlanguage|default:'en'}" {$htmlTagAttributes} {if $gBitLanguage->isLanguageRTL()}dir="rtl"{/if}>

{if $gBitThemes->mDisplayMode}
	{assign var=displayClass value=$gBitThemes->mDisplayMode|cat:"mode"|cat:" "}
{/if}
{if $gQueryUser->mUserId}
	{assign var=userClass value="user"|cat:$gQueryUser->mUserId|cat:" "}
{/if}
{if $gContent->mContentId}
	{assign var=contentClass value="cid"|cat:$gContent->mContentId}
{/if}

<head>
	<title>{$browserTitle} - {$gBitSystem->getConfig('site_title')}</title>

	{**** if the theme has a header, it goes first ****}
	{if file_exists("`$smarty.const.CONFIG_THEME_PATH`html_head_inc.tpl")}
		{include file="`$smarty.const.CONFIG_THEME_PATH`html_head_inc.tpl"}
	{/if}

	{**** get custom head files from individual packages ****}
	{foreach from=$gBitThemes->mAuxFiles.templates.html_head_inc item=file}
		{include file=$file}
	{/foreach}
</head>
<body
	{if $gBitSystem->mOnload} onload="{foreach from=$gBitSystem->mOnload item=loadString}{$loadString}{/foreach}" {/if}
	{if $gBitSystem->mOnunload} onunload="{foreach from=$gBitSystem->mOnunload item=loadString}{$loadString}{/foreach}"	{/if} 
	id="{$smarty.const.ACTIVE_PACKAGE}" class="{$displayClass}{$userClass}{$contentClass}">
{if $gBitSystem->mDebugHtml}
	<div id="bw_debughtml">
		<a href="#postdebug" onclick="document.getElementById('bw_debughtml').style.display='none';">Go to content</a><br />
		{$gBitSystem->mDebugHtml}
	</div>
	<a name="postdebug"></a>
{/if}

{strip}
	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}<div dir="rtl">{/if}

	{if $gBitSystem->isFeatureActive( 'site_left_column' ) && !$gHideModules and $gBitSystem->isFeatureActive( 'site_right_column' ) && !$gHideModules && $gBitThemes->hasColumnModules('l') && $gBitThemes->hasColumnModules('r')}
		{assign var=extraColumns value=2}
	{elseif $gBitSystem->isFeatureActive( 'site_left_column' ) && !$gHideModules && $gBitThemes->hasColumnModules('l')}
		{assign var=extraColumns value=1}
	{elseif $gBitSystem->isFeatureActive( 'site_right_column' ) && !$gHideModules && $gBitThemes->hasColumnModules('r')}
		{assign var=extraColumns value=1}
	{else}
		{assign var=extraColumns value=0}{/if}

	{if $gBitSystem->isFeatureActive( 'site_top_column' ) && !$gHideModules}
	<header class="row{$gBitSystem->getConfig('layout-header')} mainheader">
		{$gBitThemes->displayLayoutColumn('t')}
	</header>
	{/if}

	<section class="container{$gBitSystem->getConfig('layout-maincontent')} maincontent">
		<div class="row{$gBitSystem->getConfig('layout-maincontent')}">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='container' serviceHash=$gContent->mInfo}

			<div id="wrapper" class="span{math equation="12-x*3" x=$extraColumns}">
				{**** Theme Layout Modules : CENTER ****}
				{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='wrapper' serviceHash=$gContent->mInfo}
				<div id="content">
					{include file="bitpackage:liberty/display_structure.tpl"}
					{if $pageError}<div class="error">{$pageError}</div>{/if}
					{include file=$mid}
				</div><!-- end #content -->{* needed by output filters. *}
			</div><!-- end #wrapper -->

			{if $gBitSystem->isFeatureActive( 'site_left_column' ) && !$gHideModules && $gBitThemes->hasColumnModules('l')}
				{**** Theme Layout Modules : NAVIGATION ****}
				<nav id="navigation" class="span3">
					{$gBitThemes->displayLayoutColumn('l')}
				</nav><!-- end #navigation -->{* needed by output filters. *}
			{/if}

			{if $gBitSystem->isFeatureActive( 'site_right_column' ) && !$gHideModules && $gBitThemes->hasColumnModules('r')}
				{**** Theme Layout Modules : EXTRA ****}
				<nav id="extra" class="span3">
					{$gBitThemes->displayLayoutColumn('r')}
				</nav><!-- end #extra -->{* needed by output filters. *}
			{/if}
		<div>
	</section>

	<footer class="container{$gBitSystem->getConfig('layout-footer')} mainfooter">
		{**** Theme Layout Modules : BOTTOM ****}
		{if $gBitSystem->isFeatureActive( 'site_bottom_column' ) && !$gHideModules}
			{$gBitThemes->displayLayoutColumn('b')}
		{/if}
		{* get custom footer files from individual packages *}
		{foreach from=$gBitThemes->mAuxFiles.templates.footer_inc item=file}
			{include file=$file}
		{/foreach}
	</footer>

	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}</div>{/if}
	</body>
</html>
{/strip}
