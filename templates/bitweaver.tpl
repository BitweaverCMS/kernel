{include file="bitpackage:kernel/header.tpl"}
{strip}
{if $print_page ne "y"}
	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}<div dir="rtl">{/if}

	<div id="container" class="blocks{
		if $gBitSystem->isFeatureActive( 'site_left_column' ) && $l_modules && !$gHideModules and $gBitSystem->isFeatureActive( 'site_right_column' ) && $r_modules && !$gHideModules
			}3{
		elseif $gBitSystem->isFeatureActive( 'site_left_column' ) && $l_modules && !$gHideModules
			}2n{
		elseif $gBitSystem->isFeatureActive( 'site_right_column' ) && $r_modules && !$gHideModules
			}2e{
		else
			}1{
		/if
	}">

		<div id="header">
			{if $popupPage}
				{include file="bitpackage:kernel/poptop.tpl"}
			{else}
				{include file="bitpackage:kernel/top.tpl"}
				{if $gBitSystem->isFeatureActive( 'site_top_bar' )}
					{include file="bitpackage:kernel/top_bar.tpl"}
				{/if}
			{/if}

			{if $gBitSystem->isFeatureActive( 'site_top_column' ) && $t_modules && !$gHideModules}
				{section name=homeix loop=$t_modules}
					{$t_modules[homeix].data}
				{/section}
			{/if}
		</div>

		<div id="wrapper">
			<div id="content">
				{include file="bitpackage:liberty/display_structure.tpl"}
				{if $pageError}<div class="error">{$pageError}</div>{/if}
				{include file=$mid}
			</div>
		</div>

		{if $gBitSystem->isFeatureActive( 'site_left_column' ) && $l_modules && !$gHideModules}
			<div id="navigation">
				{include file="bitpackage:kernel/bit_left.tpl"}
			</div>
		{/if}

		{if $gBitSystem->isFeatureActive( 'site_right_column' ) && $r_modules && !$gHideModules}
			<div id="extra">
				{include file="bitpackage:kernel/bit_right.tpl"}
			</div>
		{/if}

		{if $gBitSystem->isFeatureActive( 'site_bot_bar' )}
			<div id="footer">
				{include file="bitpackage:kernel/bot_bar.tpl"}
			</div>
		{/if}

	</div>

	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}</div>{/if}
{/if}
{/strip}
{include file="bitpackage:kernel/footer.tpl"}
