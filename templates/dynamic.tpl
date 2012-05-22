<div id="dynamic">
	{foreach from=$gCenterPieces item=centerPiece}
	<div class="dynamicmodule {$centerPiece.module_rsrc|basename:'.tpl'|regex_replace:'/_/':'-'}">
		{assign var=moduleParams value=$centerPiece}
		{include file=$centerPiece.module_rsrc}
	</div>
	{foreachelse}
		{if $gDefaultCenter}
			{include file=$gDefaultCenter}
		{/if}
	{/foreach}
</div>
