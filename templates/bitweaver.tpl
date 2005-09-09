{include file="bitpackage:kernel/header.tpl"}
{if $print_page ne "y"}
	<a class="skip" style="position:absolute;top:0;left:-999em;width:0;height:0;" href="#content">{tr}Skip Navigation{/tr}</a>
	{if $gBitSystem->isFeatureActive( 'feature_bidi' )}
		<table dir="rtl"><tr><td>
	{/if}

	<div id="tikibody">
		{include file="bitpackage:kernel/top.tpl"}

		{if $gBitSystem->isFeatureActive( 'feature_top_bar' )}
			{include file="bitpackage:kernel/top_bar.tpl"}
		{/if}

		<table id="tikilayouttable" cellspacing="0" cellpadding="0" border="0">
			<tr>
				{include file="bitpackage:kernel/bit_left.tpl"}
				<td id="tikimain">
					<div id="tikimainfx">
						{include file="bitpackage:liberty/display_structure.tpl"}
						<a style="padding:0;margin:0;border:0;" name="content"></a>
						{if $pageError}
							<div class="error">{$pageError}</div>
						{/if}
						{include file=$mid}
					</div> <!-- end #tikimainfx -->
				</td> <!-- end #tikimain -->
				{include file="bitpackage:kernel/bit_right.tpl"}
			</tr>
		</table>

		{if $gBitSystem->isFeatureActive( 'feature_bot_bar' )}
			<div id="tikibottom">
				{include file="bitpackage:kernel/bot_bar.tpl"}
			</div> <!-- end #tikibottom -->
		{/if}
	</div> <!-- end #tikibody -->

	{if $gBitSystem->isFeatureActive( 'feature_bidi' )}
		</td></tr></table>
	{/if}

	{include file="bitpackage:kernel/footer.tpl"}
{/if}
