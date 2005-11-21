{strip}

<div class='popup box'>
	<div class='boxtitle'>
		{$title}
		{if $closebutton}
			<span class='closebutton'>
				<a onclick='javascript:return cClick();'>{biticon ipackage='liberty' iname='close' iexplain='close' iforce='icon'}</a>
			</span>
		{/if}
	</div>

	<div class='boxcontent'>
		{$content}
	</div>
</div>

{/strip}
