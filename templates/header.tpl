{strip}
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

	{if file_exists("`$smarty.const.CONFIG_THEME_PATH`header_theme_inc.tpl")}
		{include file="`$smarty.const.CONFIG_THEME_PATH`header_theme_inc.tpl"}
	{/if}

	{* get custom header files from individual packages *}
	{foreach from=$gBitThemes->mAuxFiles.templates.header_inc item=file}
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

{if $gBitSystem->isFeatureActive( 'site_help_popup' )}
	{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
{/if}

{/strip}
