{strip}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>{$browserTitle} - {$gBitSystemPrefs.site_title}</title>

	{* get custom header files from individual packages *}
	{foreach from=$gBitSystem->mStyles.headerIncFiles item=file}
		{include file=$file}
	{/foreach}
</head>
<body{if $gBodyOnload} onload="{foreach from=$gBodyOnload item=loadString}{$loadString}{/foreach}"{/if}>
<div style="display:none;position:absolute;top:0;left:-999em;"><a class="skip" style="position:absolute;top:0;left:-999em;width:0;height:0;" href="#content">{tr}Skip Navigation{/tr}</a></div>
{if $gBitSystem->isFeatureActive( 'help_popup' )}
	{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
{/if}
{/strip}
