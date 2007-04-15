{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/footer.tpl,v 1.4 2007/04/15 22:43:17 nickpalmer Exp $ *}

	{* get custom footer files from individual packages *}
	{foreach from=$gBitThemes->mStyles.footerIncFiles item=file}
		{include file=$file}
	{/foreach}

</body>
</html>  
