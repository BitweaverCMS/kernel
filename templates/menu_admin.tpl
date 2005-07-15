<h4>EVERYTHING BELOW HERE NEEDS TO BE MOVED OUT OF kernel/menu_admin.tpl AND INTO package/menu_package_admin.tpl</h4>

<div class="subsection box">
	<div class="boxtitle">{tr}Sub sections{/tr}</div>
	<div class="boxcontent">

{sortlinks}

{if $gBitSystem->isFeatureActive( 'feature_live_support' ) and ($gBitUser->hasPermission( 'bit_p_live_support_admin' ) or $user_is_operator eq 'y')}
	<li><a class="item" href="{$gBitLoc.LIVE_SUPPORT_PKG_URL}admin/index.php">{tr}Live support{/tr}</a></li>
{/if}
{if $gBitSystem->isFeatureActive( 'feature_calendar' ) and ($gBitUser->hasPermission( 'bit_p_admin_calendar' ))}
	<li><a class="item" href="{$gBitLoc.CALENDAR_PKG_URL}admin/index.php">{tr}Calendar{/tr}</a></li>
{/if}
{if $gBitUser->isAdmin()}
	{if $gBitSystem->isFeatureActive( 'feature_featuredLinks' )}
		<li><a class="item" href="{$gBitLoc.FEATURED_LINKS_PKG_URL}admin/index.php">{tr}Links{/tr}</a></li>
	{/if}{if $gBitSystem->isFeatureActive( 'feature_polls' )}
		<li><a class="item" href="{$gBitLoc.POLLS_PKG_URL}edit.php">{tr}Polls{/tr}</a></li>
	{/if}{if $gBitSystem->isFeatureActive( 'feature_theme_control' )}
		<li><a class="item" href="{$gBitLoc.THEMES_PKG_URL}theme_control.php">{tr}Theme control{/tr}</a></li>
	{/if}
{/if}{if $gBitSystem->isFeatureActive( 'feature_chat' ) and $gBitUser->hasPermission( 'bit_p_admin_chat' )}
	<li><a class="item" href="{$gBitLoc.CHAT_PKG_URL}admin/index.php">{tr}Chat{/tr}</a></li>
{/if}{if $gBitSystem->isPackageActive( 'categories' ) and $gBitUser->hasPermission( 'bit_p_admin_categories' )}
	<li><a class="item" href="{$gBitLoc.CATEGORIES_PKG_URL}admin/index.php">{tr}Categories{/tr}</a></li>
{/if}{if $gBitSystem->isFeatureActive( 'feature_banners' ) and $gBitUser->hasPermission( 'bit_p_admin_banners' )}
	<li><a class="item" href="{$gBitLoc.BANNERS_PKG_URL}admin/index.php">{tr}Banners{/tr}</a></li>
{/if}{if $gBitSystem->isFeatureActive( 'feature_drawings' ) and $gBitUser->hasPermission( 'bit_p_admin_drawings' )}
	<li><a class="item" href="{$gBitLoc.DRAWINGS_PKG_URL}admin/index.php">{tr}Drawings{/tr}</a></li>
{/if}{if $gBitSystem->isFeatureActive( 'feature_dynamic_content' ) and $gBitUser->hasPermission( 'bit_p_admin_dynamic' )}
	<li><a class="item" href="{$gBitLoc.DCS_PKG_URL}index.php">{tr}Dynamic content{/tr}</a></li>
{/if}{if $gBitSystem->isFeatureActive( 'feature_webmail' ) and $gBitUser->hasPermission( 'bit_p_admin_mailin' )}
	<li><a class="item" href="{$gBitLoc.WEBMAIL_PKG_URL}admin/admin_mailin.php">{tr}Mail-in{/tr}</a></li>
{/if}{if $gBitSystem->isFeatureActive( 'feature_html_pages' ) and $gBitUser->hasPermission( 'bit_p_edit_html_pages' )}
	<li><a class="item" href="{$gBitLoc.HTML_PKG_URL}admin/admin_html_pages.php">{tr}HTML pages{/tr}</a></li>
{/if}{if $gBitSystem->isFeatureActive( 'feature_referer_stats' ) and $gBitUser->hasPermission( 'bit_p_view_referer_stats' )}
	<li><a class="item" href="{$gBitLoc.STATS_PKG_URL}referer_stats.php">{tr}Referer stats{/tr}</a></li>
{/if}{if $gBitSystem->isFeatureActive( 'feature_integrator' ) and $gBitUser->hasPermission( 'bit_p_admin_integrator' )}
	<li><a class="item" href="{$gBitLoc.INTEGRATOR_PKG_URL}admin/index.php">{tr}Integrator{/tr}</a></li>
{/if}
{/sortlinks}

	</div>
</div>
