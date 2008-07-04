{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/footer.tpl,v 1.6 2008/07/04 18:41:09 squareing Exp $ *}
{strip}
		{* get custom footer files from individual packages *}
		{foreach from=$gBitThemes->mAuxFiles.templates.footer_inc item=file}
			{include file=$file}
		{/foreach}
	</body>
</html>
{/strip}
