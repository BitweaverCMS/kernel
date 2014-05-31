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
{strip}
<head>
	<title>{$browserTitle} - {$gBitSystem->getConfig('site_title')}</title>

	{**** if the theme has a header, it goes first ****}
	{if file_exists("`$smarty.const.CONFIG_THEME_PATH`theme_head_inc.tpl")}
		{include file="`$smarty.const.CONFIG_THEME_PATH`theme_head_inc.tpl"}
	{/if}

	{**** get custom head files from individual packages ****}
	{foreach from=$gBitThemes->mAuxFiles.templates.html_head_inc item=file}
		{include file=$file}
	{/foreach}

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="{$smarty.const.CONFIG_PKG_URL}js/html5shim/html5shiv.js"></script>
    <![endif]-->
</head>
<body
	{if $gBitSystem->mOnload} onload="{foreach from=$gBitSystem->mOnload item=loadString}{$loadString}{/foreach}" {/if}
	{if $gBitSystem->mOnunload} onunload="{foreach from=$gBitSystem->mOnunload item=loadString}{$loadString}{/foreach}"	{/if} 
	id="{$smarty.const.ACTIVE_PACKAGE}" class="{$displayClass}{$userClass}{$contentClass}">
{if $gBitSystem->mDebugHtml}
	<div id="bw_debughtml">
		<a href="#postdebug" onclick="document.getElementById('bw_debughtml').style.display='none';">Go to content</a>
		{$gBitSystem->mDebugHtml}
	</div>
	<a name="postdebug"></a>
{/if}

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
	<header class="container{$gBitSystem->getConfig('layout-header')} mainheader">
		{$gBitThemes->displayLayoutColumn('t')}
	</header>
	{/if}

	<section class="container{$gBitSystem->getConfig('layout-body')}">
			{if $gBitSystem->getConfig('site_notice')}
			<div class="sitenotice">{$gBitSystem->getConfig('site_notice')}</div>
			{/if}
			<div class="ink-grid  {$gBitSystem->getConfig('layout-maincontent')}">
				{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='row' serviceHash=$gContent->mInfo}

				{if $gBitSystem->isFeatureActive( 'site_left_column' ) && !$gHideModules && $gBitThemes->hasColumnModules('l')}
					{**** Theme Layout Modules : NAVIGATION ****}
					<nav id="navigation" class="large-20 medium-25 small-100">
						{$gBitThemes->displayLayoutColumn('l')}
					</nav><!-- end #navigation -->{* needed by output filters. *}
				{/if}

				<section id="wrapper" class="large-{math equation="100-x*20" x=$extraColumns} medium-{math equation="100-x*25" x=$extraColumns} small-100" id="content">
					{**** Theme Layout Modules : CENTER ****}
					{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='wrapper' serviceHash=$gContent->mInfo}
					{include file="bitpackage:liberty/display_structure.tpl"}
					{include file=$mid}
				</section><!-- end #wrapper -->

				{if $gBitSystem->isFeatureActive( 'site_right_column' ) && !$gHideModules && $gBitThemes->hasColumnModules('r')}
					{**** Theme Layout Modules : EXTRA ****}
					<nav id="extra" class="large-20 medium-25 small-100">
						{$gBitThemes->displayLayoutColumn('r')}
					</nav><!-- end #extra -->{* needed by output filters. *}
				{/if}
			</div>
			<div class="clear"></div>
	</section>
	
	<footer class="container{$gBitSystem->getConfig('layout-footer')} mainfooter">
		<div class="ink-grid">
			{**** Theme Layout Modules : BOTTOM ****}
			{if $gBitSystem->isFeatureActive( 'site_bottom_column' ) && !$gHideModules}
				{$gBitThemes->displayLayoutColumn('b')}
			{/if}
		</div>
		<div class="clear"></div>
	</footer>

	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}</div>{/if}
	{* get custom footer files from individual packages *}
	{foreach from=$gBitThemes->mAuxFiles.templates.footer_inc item=file}
		{include file=$file}
	{/foreach}
</body>
</html>
{/strip}
