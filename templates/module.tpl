{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/Attic/module.tpl,v 1.2 2005/07/17 17:36:07 squareing Exp $ *}
{strip}
<div class="module box {$module_name|replace:"_":"-"}">
	{if $module_title}
		<div class="boxtitle">
			{if $gBitSystem->isFeatureActive( 'feature_modulecontrols' )}
				<div class="control">
					<a title="{tr}Move module up{/tr}" href="{$current_location|escape}{$mpchar}mc_up={$module_name|escape}">
						{biticon ipackage=liberty iname="move_up" iexplain="up"}</a>
					<a title="{tr}Move module down{/tr}" href="{$current_location|escape}{$mpchar}mc_down={$module_name|escape}">
						{biticon ipackage=liberty iname="move_down" iexplain="down"}</a>
					<a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar}mc_move={$module_name|escape}">
						{biticon ipackage=liberty iname="move_left_right" iexplain="move left right"}</a>
					<a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to unassign this module?{/tr}')">
						{biticon ipackage=liberty iname="delete_small" iexplain="remove"}</a>
				</div>
			{/if}
			{if $feature_collapsible_modules eq 'y'}<a href="javascript:toggle('{$module_name}');">{/if}
				{$module_title}
			{if $feature_collapsible_modules eq 'y'}</a>{/if}
		</div>
	{/if}
	<div class="boxcontent" id="{$module_name}"{if $gBitSystem->isFeatureActive( 'feature_collapsible_modules' )} style="display:{$toggle_state};"{/if}>
	    {$module_content}
	</div>
</div>
{/strip}
