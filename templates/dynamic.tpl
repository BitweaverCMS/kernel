{section name=inc loop=$gCenterPieces }
	{assign var='c' value='c'}
	{assign var='idx' value=$smarty.section.inc.index}
	{assign var=moduleParams value=$gBitSystem->mLayout.$c.$idx}
	{include file=$gCenterPieces[inc]}
{sectionelse}
	{if $gDefaultCenter}
		{include file=$gDefaultCenter}
	{/if}
{/section}
