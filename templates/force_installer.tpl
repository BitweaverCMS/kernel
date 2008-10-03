{if ($gBrowserInfo.browser neq 'ie') or ($gBrowserInfo.browser eq 'ie' and $gBrowserInfo.maj_ver gt 7) }
<?xml version="1.0" encoding="utf-8"?>
{/if}
{strip}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style type="text/css">
		{* hidden from 4.x browsers: *}
		@import "{$smarty.const.INSTALL_PKG_URL}style/install.css";
	</style>
	<title>Upgrade Bitweaver</title>
	<link rel="shortcut icon" href="{$smarty.const.INSTALL_PKG_URL}favicon.ico" type="image/x-icon" />
	<link rel="icon" href="{$smarty.const.INSTALL_PKG_URL}favicon.ico" type="image/x-icon" />

	{* if $gBrowserInfo.browser eq 'ie'}
		<!--[if lt IE 7]>
			<script type="text/javascript" src="{$smarty.const.BIT_ROOT_URL}util/javascript/fixes/ie7/IE8.js"></script>
		<![endif]-->
	{/if *}
</head>
<body id="step{$smarty.request.step}">
	<div id="container">
		<div id="wrapper">
			<div id="content">
				{if $gBitUser->isAdmin()}
					<h1>Upgrade Bitweaver</h1>
					{legend legend=""}
						<div class="center">
							<a href="http://www.bitweaver.org/">
								<img src="{$smarty.const.INSTALL_PKG_URL}style/images/bitweaver_logo-trans.png" width="121" height="121" alt="bitweaver logo" title="Click here to visit the upgrade instructions" />
							</a>
						</div>
						<p class="danger">You have just updated your bitweaver code to <strong>version {$smarty.const.BIT_MAJOR_VERSION}.{$smarty.const.BIT_MINOR_VERSION}.{$smarty.const.BIT_SUB_VERSION} {$smarty.const.BIT_LEVEL}</strong> and now some fixes to the database must be performed before you can continue.</p>
						<ul class="help">
							<li>Please visit the <strong><a href="{$smarty.const.INSTALL_PKG_URL}install.php?step=4">Installer</a></strong> to continue.</li>
							<li>If you wish to find out more about this upgrade, please visit our <a class="external" href="http://www.bitweaver.org/wiki/upgrade">Upgrade page</a></li>
						</ul>

						<hr />
						<h3>Troubleshooting</h3>
						<p>If you can not visit the installer, please make sure that you've reverted all security measures such as permissions or renaming files. Reverting common security measures might include:</p>
						<ul>
							<li>
								<strong>Linux</strong><br />
								<p><code>
									cd {$smarty.const.BIT_ROOT_PATH}<br />
									chmod -R 755 install/<br />
									find install/ -type f -print | xargs chmod 644
								</code></p>
							</li>
							<li>
								<strong>Windows</strong>
								<p>If you've renamed your <kbd>install/install_inc.php</kbd> to something else, you need to revert this change.</p>
							</li>
						</ul>
					{/legend}
				{else}
					{include file=bitpackage:install/install_login.tpl title="Only site adminstrators can access this website at this time."}
				{/if}
			</div>
		</div>
	</div>
</body>
</html>
{/strip}
