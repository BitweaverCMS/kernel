{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/footer.tpl,v 1.3 2005/10/12 15:13:51 spiderr Exp $ *}

	{* get custom footer files from individual packages *}
	{foreach from=$gBitSystem->mStyles.footerIncFiles item=file}
		{include file=$file}
	{/foreach}

</body>
</html>  
