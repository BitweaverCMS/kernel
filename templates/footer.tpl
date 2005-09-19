{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/footer.tpl,v 1.1.1.1.2.3 2005/09/19 10:40:09 wolff_borg Exp $ *}

	{* get custom footer files from individual packages *}
	{foreach from=$gBitSystem->mStyles.footerIncFiles item=file}
		{include file=$file}
	{/foreach}

</body>
</html>  
