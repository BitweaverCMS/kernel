{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/header.tpl,v 1.8 2005/07/17 17:36:06 squareing Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>{$browserTitle} - {$siteTitle}</title>

	{* get custom header files from individual packages *}
	{foreach from=$gBitLoc.headerIncFiles item=file}
		{include file=$file}
	{/foreach}
</head>
<body>
{if $minical_reminders>100}
	<iframe width="0" height="0" border="0" src="{$gBitLoc.CALENDAR_PKG_URL}minical_reminders.php" />
{/if}
{if $gBitSystem->isFeatureActive( 'feature_helppopup' )}
	{popup_init src="`$gBitLoc.THEMES_PKG_URL`js/overlib.js"}
{/if}
