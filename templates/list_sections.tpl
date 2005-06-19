<div class="admin box">
  <div class="boxtitle">{tr}Package Settings{/tr}</div>
  <div class="boxcontent">
    <table width="100%"><tr>
    {assign var="i" value="1"}
    {foreach name=admin_panels key=key item=item from=$admin_panels}
    {if $item.adminPanel}
      <td width="25%" style="text-align:center;vertical-align:top;"><a href="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page={$item.adminPanel}" title="{tr}{$item.title}{/tr}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons/admin_{$item.adminPanel}.png" alt="{tr}{$item.title}{/tr}" /><br />{tr}{$item.title}{/tr}</a></td>
      {if not ($i++ mod 4)}
        </tr><tr>
      {/if}
    {/if}
    {/foreach}
    </tr></table>
  </div>
</div>
