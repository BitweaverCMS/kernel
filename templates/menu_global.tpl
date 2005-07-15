{if $gBitSystem->isFeatureActive( 'feature_calendar' ) and $gBitUser->hasPermission( 'bit_p_view_calendar' )}
	<a class="menuoption" href="{$gBitLoc.CALENDAR_PKG_URL}index.php">{tr}Calendar{/tr}</a>
{/if}
{if $gBitSystem->isFeatureActive( 'feature_contact' )}
	<a class="menuoption" href="{$gBitLoc.MESSU_PKG_URL}contact.php">{tr}Contact us{/tr}</a>
{/if}
{if $gBitSystem->isFeatureActive( 'feature_stats' ) and $gBitUser->hasPermission( 'bit_p_view_stats' )}
	<a class="menuoption" href="{$gBitLoc.STATS_PKG_URL}index.php">{tr}Statistics{/tr}</a>
{/if}
