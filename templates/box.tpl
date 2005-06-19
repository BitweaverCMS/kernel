{strip}

<div class="{$class|default:"box"}" {$atts}>

	{if $title or ($ipackage and $iname)}
		<div class="boxtitle">
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
		</div>
	{/if}

	<div class="boxcontent">
		{$content}
	</div>
</div>

{/strip}
