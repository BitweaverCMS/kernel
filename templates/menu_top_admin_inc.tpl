{strip}<a tabindex="-1" accesskey="A" class="{if $smarty.const.ACTIVE_PACKAGE eq 'kernel'} active{/if}" href="{if $gBitUser->isAdmin()}{$smarty.const.KERNEL_PKG_URL}admin/index.php{else}#{/if}">
<i class="icon-cog"></i> {tr}Administration{/tr}
</a>
<ul class="dropdown-menu sub-menu pull-right">	
{foreach key=key item=menu from=$adminMenu}
	{if $key eq 'kernel' or $key eq 'liberty' or $key eq 'users' or $key eq 'themes'}
	<li class="dropdown-submenu favorite">{include file=$menu.tpl packageMenuTitle=$key packageMenuClass="dropdown-menu sub-menu"}</li>
	{/if}
{/foreach}
{foreach key=key item=menu from=$adminMenu}
	{if $key neq 'kernel' and $key neq 'liberty' and $key neq 'users' and $key neq 'themes'}
	<li class="dropdown-submenu">{include file=$menu.tpl packageMenuTitle=$key packageMenuClass="dropdown-menu sub-menu"}</li>
	{/if}
{/foreach}
</ul>
{/strip}
