<div id="dynamic">
	{foreach from=$gCenterPieces item=centerPiece}
		{assign var=moduleParams value=$centerPiece}
		{include file=$centerPiece.module_rsrc}
	{foreachelse}
		{if $gDefaultCenter}
			{include file=$gDefaultCenter}
		{/if}
	{/foreach}
</div>
