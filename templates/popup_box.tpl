{strip}

<div class='popup box'>
	<h3 class='boxtitle'>
		{$title}
		{if $closebutton}
			<span class='closebutton'>
				<a onclick='javascript:return cClick();'>{biticon ipackage="icons" iname="window-close" iexplain="close" iforce="icon"}</a>
			</span>
		{/if}
	</h3>

	<div class='boxcontent'>
		{$content}
	</div>
</div>

{/strip}
