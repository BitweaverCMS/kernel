<?php

$tables = array(

'adodb_logsql' => "
	created T NOT NULL,
	sql0 C(250) NOTNULL,
	sql1 X NOTNULL,
	params X NOTNULL,
	tracer X NOTNULL,
	timer N(16.6) NOTNULL
",

'kernel_config' => "
	config_name C(40) PRIMARY,
	package C(100),
	config_value C(250)
",

'mail_notifications' => "
	event C(200),
	object C(200),
	email C(200)
",


);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( KERNEL_PKG_NAME, $tableName, $tables[$tableName], TRUE );
}

$gBitInstaller->registerPackageInfo( KERNEL_PKG_NAME, array(
	'description' => "This is the heart of the application. Without this --&gt; nothing.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( KERNEL_PKG_NAME, array(
	array(KERNEL_PKG_NAME,'site_online_help','y'),
	array(KERNEL_PKG_NAME,'site_edit_help','y'),
	array(KERNEL_PKG_NAME,'site_form_help','y'),
	array(KERNEL_PKG_NAME,'site_short_date_format','%d %b %Y'),
	array(KERNEL_PKG_NAME,'site_short_time_format','%H:%M %Z'),
	//array(KERNEL_PKG_NAME,'site_title',''),
	array(KERNEL_PKG_NAME,'site_upload_dir','storage'),
	array(KERNEL_PKG_NAME,'site_temp_dir','temp'),
	//array(KERNEL_PKG_NAME,'site_use_proxy','n'),
	//array(KERNEL_PKG_NAME,'site_proxy_host',''),
	//array(KERNEL_PKG_NAME,'site_proxy_port',''),
	//array(KERNEL_PKG_NAME,'site_user_assigned_modules','n'),
	//array(KERNEL_PKG_NAME,'site_http_domain',''),
	array(KERNEL_PKG_NAME,'site_http_port','80'),
	array(KERNEL_PKG_NAME,'site_http_prefix','/'),
	//array(KERNEL_PKG_NAME,'site_https_domain',''),
	//array(KERNEL_PKG_NAME,'site_https_login','n'),
	//array(KERNEL_PKG_NAME,'site_https_login_required','n'),
	array(KERNEL_PKG_NAME,'site_https_port','443'),
	array(KERNEL_PKG_NAME,'site_https_prefix','/'),
	//array(KERNEL_PKG_NAME,'banning','n'),
	array(KERNEL_PKG_NAME,'users_count_admin_pageviews','y'),
	//array(KERNEL_PKG_NAME,'site_direct_pagination','n'),
	array(KERNEL_PKG_NAME,'site_display_timezone','UTC'),
	array(KERNEL_PKG_NAME,'site_long_date_format','%A %d of %B, %Y'),
	array(KERNEL_PKG_NAME,'site_long_time_format','%H:%M:%S %Z'),
	array(KERNEL_PKG_NAME,'site_left_column','y'),
	array(KERNEL_PKG_NAME,'site_right_column','y'),
	array(KERNEL_PKG_NAME,'max_records','10'),
	array(KERNEL_PKG_NAME,'language','en'),
	array(KERNEL_PKG_NAME,'site_sender_email',''),
	array(KERNEL_PKG_NAME,'site_url_index',''),
) );

$moduleHash = array(
	'mod_bitweaver_info' => array(
		'title' => 'bitweaver',
		'ord' => 1,
		'pos' => 'r',
		'module_rsrc' => 'bitpackage:kernel/mod_bitweaver_info.tpl'
	),
	'mod_server_stats' => array(
		'title' => 'Server Statistics',
		'groups' => array( 'Admin' ),
		'ord' => 2,
		'pos' => 'r',
		'module_rsrc' => 'bitpackage:kernel/mod_server_stats.tpl'
	),
	'mod_powered_by' => array(
		'title' => 'Powered by',
		'ord' => 4,
		'pos' => 'r',
		'module_rsrc' => 'bitpackage:kernel/mod_powered_by.tpl'
	),
	'mod_package_menu' => array(
		'title' => NULL,
		'ord' => 1,
		'pos' => 'l',
		'module_rsrc' => 'bitpackage:kernel/mod_package_menu.tpl'
	)
);

$gBitInstaller->registerModules( $moduleHash );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( KERNEL_PKG_NAME, array(
	array('p_admin', 'Can manage users groups and permissions and all aspects of site management', 'admin', KERNEL_PKG_NAME ),
	array('p_access_closed_site', 'Can access site when closed', 'admin', KERNEL_PKG_NAME)
) );

?>
