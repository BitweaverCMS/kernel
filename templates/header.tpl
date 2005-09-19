{strip}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>{$browserTitle} - {$siteTitle}</title>

	{* get custom header files from individual packages *}
	{foreach from=$gBitSystem->mStyles.headerIncFiles item=file}
		{include file=$file}
	{/foreach}
</head>
<body>
{if $gBitUser->hasPermission( 'bit_p_debug_console' ) and $gBitSystem->isPackageActive( 'debug' )}
	{include file="bitpackage:debug/debug_console.tpl"}
{/if}
{if $gBitSystem->isFeatureActive( 'feature_helppopup' )}
	{popup_init src="`$smarty.const.THEMES_PKG_URL`js/overlib.js"}
{/if}
{/strip}
