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

'kernel_prefs' => "
	name C(40) PRIMARY,
	package C(100),
	pref_value C(250)
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
	'version' => '0.1',
	'state' => 'experimental',
	'dependencies' => '',
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( KERNEL_PKG_NAME, array(
	array(KERNEL_PKG_NAME,'help','y'),
	array(KERNEL_PKG_NAME,'wiki_help','y'),
	array(KERNEL_PKG_NAME,'help_notes','y'),
	array(KERNEL_PKG_NAME,'short_date_format','%d %b %Y'),
	array(KERNEL_PKG_NAME,'short_time_format','%H:%M %Z'),
	//array(KERNEL_PKG_NAME,'site_title',''),
	array(KERNEL_PKG_NAME,'centralized_upload_dir','storage'),
	array(KERNEL_PKG_NAME,'temp_dir','temp'),
	//array(KERNEL_PKG_NAME,'use_proxy','n'),
	//array(KERNEL_PKG_NAME,'proxy_host',''),
	//array(KERNEL_PKG_NAME,'proxy_port',''),
	//array(KERNEL_PKG_NAME,'user_assigned_modules','n'),
	//array(KERNEL_PKG_NAME,'http_domain',''),
	array(KERNEL_PKG_NAME,'http_port','80'),
	array(KERNEL_PKG_NAME,'http_prefix','/'),
	//array(KERNEL_PKG_NAME,'https_domain',''),
	//array(KERNEL_PKG_NAME,'https_login','n'),
	//array(KERNEL_PKG_NAME,'https_login_required','n'),
	array(KERNEL_PKG_NAME,'https_port','443'),
	array(KERNEL_PKG_NAME,'https_prefix','/'),
	array(KERNEL_PKG_NAME,'bot_bar','y'),
	array(KERNEL_PKG_NAME,'top_bar','y'),
	//array(KERNEL_PKG_NAME,'banning','n'),
	//array(KERNEL_PKG_NAME,'site_contact','n'),
	array(KERNEL_PKG_NAME,'jstabs','y' ),
	array(KERNEL_PKG_NAME,'contact_user','admin'),
	array(KERNEL_PKG_NAME,'count_admin_pvs','y'),
	//array(KERNEL_PKG_NAME,'direct_pagination','n'),
	array(KERNEL_PKG_NAME,'display_timezone','UTC'),
	array(KERNEL_PKG_NAME,'long_date_format','%A %d of %B, %Y'),
	array(KERNEL_PKG_NAME,'long_time_format','%H:%M:%S %Z'),
	array(KERNEL_PKG_NAME,'left_column','y'),
	array(KERNEL_PKG_NAME,'right_column','y'),
	array(KERNEL_PKG_NAME,'max_records','10'),
	array(KERNEL_PKG_NAME,'language','en'),
	array(KERNEL_PKG_NAME,'sender_email',''),
	array(KERNEL_PKG_NAME,'url_index',''),
	//array(THEMES_PKG_NAME,'bidirectional_text','n' ),
	array(THEMES_PKG_NAME,'slide_style', DEFAULT_THEME ),
	array(THEMES_PKG_NAME,'style', DEFAULT_THEME ),
	array(THEMES_PKG_NAME,'top_bar_dropdown','y' )
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
	array('bit_p_admin', 'Can manage users groups and permissions and all aspects of site management', 'admin', KERNEL_PKG_NAME ),
	array('bit_p_access_closed_site', 'Can access site when closed', 'admin', KERNEL_PKG_NAME)
) );

?>
