{include file="bitpackage:kernel/header.tpl"}
{strip}
{if $print_page ne "y"}
	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}
		<div dir="rtl">
	{/if}

	<div id="bitbody">
		{include file="bitpackage:kernel/top.tpl"}

		{if $gBitSystem->isFeatureActive( 'top_bar' )}
			{include file="bitpackage:kernel/top_bar.tpl"}
		{/if}

		<table id="bitlayouttable" cellspacing="0" cellpadding="0" border="0">
			<tr>
				{include file="bitpackage:kernel/bit_left.tpl"}
				<td id="bitmain">
					<div id="bitmainfx">
						{include file="bitpackage:liberty/display_structure.tpl"}
						<a style="padding:0;margin:0;border:0;" name="content"></a>
						{if $pageError}
							<div class="error">{$pageError}</div>
						{/if}
						{include file=$mid}
					</div> <!-- end #bitmainfx -->
				</td> <!-- end #bitmain -->
				{include file="bitpackage:kernel/bit_right.tpl"}
			</tr>
		</table>

		{if $gBitSystem->isFeatureActive( 'bot_bar' )}
			<div id="bitbottom">
				{include file="bitpackage:kernel/bot_bar.tpl"}
			</div> <!-- end #bitbottom -->
		{/if}
	</div> <!-- end #bitbody -->

	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}
		</div>
	{/if}

	{include file="bitpackage:kernel/footer.tpl"}
{/if}
{/strip}
