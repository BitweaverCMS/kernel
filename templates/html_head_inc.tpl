{strip}
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="generator" content="bitweaver - http://www.bitweaver.org">

{if $metaDescription}
<meta name="description" content="{$metaDescription|escape}">
{elseif $smarty.server.REQUEST_URI==$smarty.const.BIT_ROOT_URL}
<meta name="description" content="{$gBitSystem->getConfig('site_description')|escape}"/>
{elseif !empty($gContent) && $gContent->isValid()}
<meta name="description" content="{$gContent->generateDescription()|strip_tags|escape}"/>
{/if}

{if $metaKeywords}
<meta name="keywords" content="{$metaKeywords|escape}">
{elseif $smarty.server.REQUEST_URI==BIT_ROOT_URI}
<meta name="keywords" content="{$metaKeywords|default:$gBitSystem->getConfig('site_keywords')}">
{elseif !empty($gContent) && $gContent->isValid()}
<meta name="keywords" content="{','|implode:$gContent->generateKeywords()|strip_tags|escape}"/>
{/if}

{if $canonicalLink}
<link rel="canonical" href="{$canonicalLink|escape}"/>
{/if}
{if !empty($relTags)}
{$relTags}
{/if}
{if $gBitSystem->isIndexed()}
<meta name="robots" content="index,follow">
{else}
<meta name="robots" content="noindex,nofollow">
{/if}

{if $gBitSystem->isFeatureActive( 'site_header_extended_nav' )}
	<link rel="start" title="{$gBitSystem->getConfig('site_title')} {tr}Home{/tr}" href="{$smarty.const.BIT_ROOT_URL}">

	{if $gBitSystem->isFeatureActive( 'site_header_help' )}
		<link rel="help" title="{tr}Help{/tr}" href="{$gBitSystem->getConfig('site_header_help')}">
	{/if}
	{if $gBitSystem->isFeatureActive( 'site_header_copyright' )}
		<link rel="copyright" title="{tr}Copyright{/tr}" href="{$gBitSystem->getConfig('site_header_copyright')}">
	{/if}
	{if $gBitSystem->isFeatureActive( 'site_header_contents' )}
		<link rel="contents" title="{tr}Contents{/tr}" href="{$gBitSystem->getConfig('site_header_contents')}">
	{/if}
	{if $gBitSystem->isFeatureActive( 'site_header_index' )}
		<link rel="index" title="{tr}Index{/tr}" href="{$gBitSystem->getConfig('site_header_index')}">
	{/if}
	{if $gBitSystem->isFeatureActive( 'site_header_glossary' )}
		<link rel="glossary" title="{tr}Glossary{/tr}" href="{$gBitSystem->getConfig('site_header_glossary')}">
	{/if}

	{assign var=headPageUrl value="`$smarty.server.SCRIPT_NAME`?sort_mode=`$listInfo.sort_mode`&amp;find=`$listInfo.find`"}
	{if $listInfo.current_page > 1}
		<link rel="first" title="{tr}First page{/tr}" href="{$headPageUrl}&amp;list_page=1">
		<link rel="previous" title="{tr}Previous page{/tr}" href="{$headPageUrl}&amp;list_page={$listInfo.current_page-1}">
	{/if}
	{if $listInfo.current_page < $listInfo.total_pages}
		<link rel="next" title="{tr}Next page{/tr}" href="{$headPageUrl}&amp;list_page={$listInfo.current_page+1}">
		<link rel="last" title="{tr}Last page{/tr}" href="{$headPageUrl}&amp;list_page={$listInfo.total_pages}">
	{/if}
{/if}

<script>/* <![CDATA[ */
	BitSystem = {
		"urls":{
			"root":"{$smarty.const.BIT_ROOT_URL}",
			"cookie":"{$smarty.const.BIT_ROOT_URL}",
		}
	} ;
	var bitCookiePath = "{$smarty.const.BIT_ROOT_URL}";
	var bitCookieDomain = "";
	var bitRootUrl = "{$smarty.const.BIT_ROOT_URL}";
	var bitTk = "{$gBitUser->mTicket}";
/* ]]> */</script>

{/strip}
