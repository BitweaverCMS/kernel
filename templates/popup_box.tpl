{strip}

<div class='popup box'>
	<h3>
		{$title}
		{if $closebutton}
			<a class="closebutton" onclick='javascript:return cClick();'>{biticon ipackage="icons" iname="window-close" iexplain="close" iforce="icon"}</a>
		{/if}
	</h3>

	<div class='boxcontent'>
		{$content}
	</div>
</div>

{/strip}
