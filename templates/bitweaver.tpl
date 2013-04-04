{include file="bitpackage:kernel/header.tpl"}
{strip}
{if $print_page ne "y"}
	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}<div dir="rtl">{/if}

	{if $gBitSystem->isFeatureActive( 'site_left_column' ) && $l_modules && !$gHideModules and $gBitSystem->isFeatureActive( 'site_right_column' ) && $r_modules && !$gHideModules
		}{assign var=extraColumns value=2}{
	elseif $gBitSystem->isFeatureActive( 'site_left_column' ) && $l_modules && !$gHideModules
		}{assign var=extraColumns value=1}{
	elseif $gBitSystem->isFeatureActive( 'site_right_column' ) && $r_modules && !$gHideModules
		}{assign var=extraColumns value=1}{
	else
		}{assign var=extraColumns value=0}{
	/if}

	<header>
		{if $gBitSystem->isFeatureActive( 'site_top_column' ) && $t_modules && !$gHideModules}
			{section name=homeix loop=$t_modules}
				{$t_modules[homeix].data}
			{/section}
		{/if}
	</header>

	<section class="row maincontent">
		<div class="container">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='container' serviceHash=$gContent->mInfo}

			<div id="wrapper" class="container span{math equation="12-x*3" x=$extraColumns}">
				{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='wrapper' serviceHash=$gContent->mInfo}
				<div id="content">
					{include file="bitpackage:liberty/display_structure.tpl"}
					{if $pageError}<div class="error">{$pageError}</div>{/if}
					{include file=$mid}
				</div><!-- end #content -->{* needed by output filters. *}
			</div><!-- end #wrapper -->

			{if $gBitSystem->isFeatureActive( 'site_left_column' ) && $l_modules && !$gHideModules}
				<nav id="navigation" class="span3">
					{include file="bitpackage:kernel/bit_left.tpl"}
				</nav><!-- end #navigation -->{* needed by output filters. *}
			{/if}

			{if $gBitSystem->isFeatureActive( 'site_right_column' ) && $r_modules && !$gHideModules}
				<nav id="extra" class="span3">
					{include file="bitpackage:kernel/bit_right.tpl"}
				</nav><!-- end #extra -->{* needed by output filters. *}
			{/if}
		<div>
	</section>

	<footer>
		{if $gBitSystem->isFeatureActive( 'site_bottom_column' ) && $b_modules && !$gHideModules}
			{section name=homeix loop=$b_modules}
				{$b_modules[homeix].data}
			{/section}
		{/if}
	</footer>

	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}</div>{/if}
{/if}
{/strip}
{include file="bitpackage:kernel/footer.tpl"}
