{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/footer.tpl,v 1.5 2008/01/13 23:14:48 nickpalmer Exp $ *}

	{* get custom footer files from individual packages *}
	{foreach from=$gBitThemes->mAuxFiles.templates.footer_inc item=file}
		{include file=$file}
	{/foreach}

</body>
</html>  
