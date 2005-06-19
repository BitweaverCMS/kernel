{if $gBitSystemPrefs.feature_calendar eq 'y' and $gBitUser->hasPermission( 'bit_p_view_calendar' )}
	<a class="menuoption" href="{$gBitLoc.CALENDAR_PKG_URL}index.php">{tr}Calendar{/tr}</a>
{/if}
{if $gBitSystemPrefs.feature_contact eq 'y'}
	<a class="menuoption" href="{$gBitLoc.MESSU_PKG_URL}contact.php">{tr}Contact us{/tr}</a>
{/if}
{if $gBitSystemPrefs.feature_stats eq 'y' and $gBitUser->hasPermission( 'bit_p_view_stats' )}
	<a class="menuoption" href="{$gBitLoc.STATS_PKG_URL}index.php">{tr}Statistics{/tr}</a>
{/if}
