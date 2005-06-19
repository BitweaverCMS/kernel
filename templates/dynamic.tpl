{section name=inc loop=$gCenterPieces}
{include file=$gCenterPieces[inc]}
{sectionelse}
{if $gDefaultCenter}
{include file=$gDefaultCenter}
{/if}
{/section}
