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
<div style="display:none;position:absolute;top:0;left:-999em;"><a class="skip" style="position:absolute;top:0;left:-999em;width:0;height:0;" href="#content">{tr}Skip Navigation{/tr}</a></div>
{if $gBitSystem->isFeatureActive( 'feature_helppopup' )}
	{popup_init src="`$smarty.const.THEMES_PKG_URL`js/overlib.js"}
{/if}
{/strip}
