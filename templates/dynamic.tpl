{foreach from=$gCenterPieces item=centerPiece}
	{assign var=moduleParams value=$centerPiece}
	{include file=$centerPiece.module_rsrc}
{foreachelse}
	{if $gDefaultCenter}
		{include file=$gDefaultCenter}
	{/if}
{/foreach}

{*
{section name=inc loop=$gCenterPieces}
	{assign var='c' value='c'}
	{assign var='idx' value=$smarty.section.inc.index}
	{assign var=moduleParams value=$gBitSystem->mLayout.$c.$idx}
	{include file=$gCenterPieces[inc]}
{sectionelse}
	{if $gDefaultCenter}
		{include file=$gDefaultCenter}
	{/if}
{/section}
*}
