{* Index we display a wiki page here *}
{include file="bitpackage:kernel/header.tpl"}
{if $gBitSystem->isFeatureActive( 'feature_bidi' )}
<table dir="rtl"><tr><td>
{/if}

<div id="bitbody">

{include file="bitpackage:kernel/top.tpl"}

{if $gBitSystem->isFeatureActive( 'feature_top_bar' )}
    {include file="bitpackage:kernel/top_bar.tpl"}
{/if}

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
{if $gBitSystem->isFeatureActive( 'feature_left_column' ) && $l_modules}
  <td id="bitleft">
    {section name=homeix loop=$l_modules}
      {$l_modules[homeix].data}
    {/section}
  </td>
{/if}
  <td id="bitmain">
    <div id="bitmainfx">
      <div class="error">
        <div class="admin box">
          <div class="boxtitle">{tr}Error{/tr}</div>
          <div class="boxcontent">
            {$msg}<br /><br />
            <form action="{$self}{if $query}?{$query|escape}{/if}" method="post">
            {foreach key=k item=i from=$post}
            <input type="hidden" name="{$k}" value="{$i|escape}" />
            {/foreach}
            <input type="submit" name="ticket_action_button" value="{tr}Click here to confirm your action{/tr}" />
            </form><br /><br />
            <a href="javascript:history.back()">{tr}Go back{/tr}</a><br /><br />
            <a href="{$bitIndex}">{tr}Return to home page{/tr}</a>
          </div>
        </div>
      </div>
    </div>
  </td>
{if $gBitSystem->isFeatureActive( 'feature_right_column' ) && $r_modules}
  <td id="bitright">
    {section name=homeix loop=$r_modules}
      {$r_modules[homeix].data}
    {/section}
  </td>
{/if}
</tr>
</table>

{if $gBitSystem->isFeatureActive( 'feature_bot_bar' )}
  <div id="bitbottom">
    {include file="bitpackage:kernel/bot_bar.tpl"}
  </div>
{/if}

</div> {* end #bitbody *}

{if $gBitSystem->isFeatureActive( 'feature_bidi' )}
</td></tr></table>
{/if}
{include file="bitpackage:kernel/footer.tpl"}
