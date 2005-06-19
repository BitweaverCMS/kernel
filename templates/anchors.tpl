{assign var="i" value="1"}
{foreach name=admin_panels key=key item=item from=$admin_panels}
	{if $item.adminPanel}
		<a href="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page={$item.adminPanel}" title="{tr}{$item.title}{/tr}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons/admin_{$item.adminPanel}.png" alt="{tr}{$item.title}{/tr}" /></a>
	{/if}
{/foreach}
