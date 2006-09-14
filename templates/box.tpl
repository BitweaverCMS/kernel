{strip}

<div class="{$class|default:"box"}" {$atts}>

	{if $title or ($ipackage and $iname)}
		<h3 class="boxtitle">
			{if $ipackage and $iname}
				{if $idiv}
					<div class="{$idiv}">
				{/if}

				{biticon ipackage=$ipackage iname=$iname iexplain=$iexplain iforce=icon} 

				{if $idiv}
					</div>
				{/if}
			{/if}
			{$title}
		</h3>
	{/if}

	<div class="boxcontent">
		{$content}
	</div>
</div>

{/strip}
