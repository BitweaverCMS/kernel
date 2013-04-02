{strip}

<div class='popup box'>
	<h3>
		{$title}
		{if $closebutton}
			<a class="closebutton" onclick='javascript:return cClick();'>{booticon iname="icon-remove"  ipackage="icons"  iexplain="close" iforce="icon"}</a>
		{/if}
	</h3>

	<div class='boxcontent'>
		{$content}
	</div>
</div>

{/strip}
