{* $Header$ *}
{strip}
		{* get custom footer files from individual packages *}
		{foreach from=$gBitThemes->mAuxFiles.templates.footer_inc item=file}
			{include file=$file}
		{/foreach}
	</body>
</html>
{/strip}
