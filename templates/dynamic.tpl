<div id="dynamic">
	{foreach from=$gCenterPieces item=centerPiece}
	<div class="dynamicmodule">
		{assign var=moduleParams value=$centerPiece}
		{include file=$centerPiece.module_rsrc}
	</div>
	{foreachelse}
		{if $gDefaultCenter}
			{include file=$gDefaultCenter}
		{/if}
	{/foreach}
</div>
