{strip}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$bitlanguage|default:'en'}" lang="{$bitlanguage|default:'en'}">
<head>
	<title>{$browserTitle} - {$gBitSystem->getConfig('site_title')}</title>

	{* get custom header files from individual packages *}
	{foreach from=$gBitThemes->mAuxFiles.templates.header_inc item=file}
		{include file=$file}
	{/foreach}
</head>
<body{if $gBitSystem->mOnload} onload="{foreach from=$gBitSystem->mOnload item=loadString}{$loadString}{/foreach}"{/if} {if $gBitSystem->mOnunload} onunload="{foreach from=$gBitSystem->mOnunload item=loadString}{$loadString}{/foreach}"{/if} id="{$smarty.const.ACTIVE_PACKAGE}" {if $gQueryUser}class="user{$gQueryUser->mUserId}"{/if}>
{if $gBitSystem->isFeatureActive( 'site_help_popup' )}
	{popup_init src="`$smarty.const.UTIL_PKG_URL`javascript/libs/overlib.js"}
{/if}
{/strip}
