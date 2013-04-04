<div class="container" id="bittop">
	<div class="navbar">
		<a class="brand" href="{$smarty.const.BIT_ROOT_URL}" {if $gBitSystem->getConfig('site_slogan')} title="{$gBitSystem->getConfig('site_slogan')|escape}" {/if}>{$gBitSystem->getConfig('site_title')}</a>
		<div class="pull-right">
			{if $gBitUser->isRegistered()}
			<ul class="nav nav-pills">
				<li class="active dropdown">
					<a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><i class="icon-user"></i> {displayname hash=$gBitUser->mInfo nolink=1} <b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<li><a href="{$gBitUser->getDisplayUrl()}">{tr}My Profile{/tr}</a></li>
						<li><a href="{$smarty.const.USERS_PKG_URL}my.php">{tr}My Account{/tr}</a></li>
						<li><a href="{$smarty.const.USERS_PKG_URL}logout.php">{tr}Logout{/tr}</a></li>
						{if $adminMenu}
							<li class="dropdown-submenu menu-admin">{include file="bitpackage:kernel/menu_top_admin_inc.tpl"}</li>
						{/if}
					</ul>
				</li>
			</ul>
			{else}
				<a href="{$smarty.const.USERS_PKG_URL}login.php">{tr}login{/tr}</a>
				{if $gBitSystem->isFeatureActive( 'users_allow_register' )}
					{tr}or{/tr} <a href="{$smarty.const.USERS_PKG_URL}register.php">{tr}register{/tr}</a> 
				{/if}
			{/if}
		</div>
	</div>
</div>
