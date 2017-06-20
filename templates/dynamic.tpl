<div id="dynamic">
	{foreach from=$gCenterPieces item=centerPiece}
	<div class="dynamicmodule {$centerPiece.module_rsrc|basename:'.tpl'|regex_replace:'/_/':'-'}">
		{include file=$centerPiece.module_rsrc moduleParams=$centerPiece compile_id=$centerPiece.module_id}
	</div>
	{foreachelse}
		{if $gDefaultCenter}
			{include file=$gDefaultCenter}
		{/if}
	{/foreach}
</div>
