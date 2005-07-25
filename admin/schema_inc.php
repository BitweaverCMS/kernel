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

'tiki_banning' => "
  ban_id I4 AUTO PRIMARY,
  mode C(4),
  title C(200),
  ip1 C(3),
  ip2 C(3),
  ip3 C(3),
  ip4 C(3),
  `user` C(40),
  date_from T NOTNULL,
  date_to T NOTNULL,
  use_dates C(1),
  created I8,
  message X
",

'tiki_banning_sections' => "
  ban_id I4 PRIMARY,
  section C(100) PRIMARY
",


'tiki_content_templates' => "
  template_id I4 AUTO PRIMARY,
  content X,
  name C(200),
  created I8
",

'tiki_content_templates_sections' => "
  template_id I4 PRIMARY,
  section C(160) PRIMARY
",

'tiki_cookies' => "
  cookie_id I4 AUTO PRIMARY,
  cookie C(255)
",

'tiki_dsn' => "
  dsn_id I4 AUTO PRIMARY,
  name C(200) NOTNULL,
  dsn C(255)
",

'tiki_dynamic_variables' => "
  name C(40) PRIMARY,
  data X
",

'tiki_layouts' => "
  user_id I4 NOTNULL,
  module_id I4 NOTNULL,
  layout C(160) NOTNULL DEFAULT 'home',
  position C(1) NOTNULL,
  rows I4,
  params C(255),
  ord I4 NOTNULL DEFAULT '1'
",

'tiki_layouts_modules' => "
  module_id I4 PRIMARY,
  availability C(1),
  title C(255),
  cache_time I8,
  rows I4,
  params C(255),
  groups X
",

'tiki_mail_events' => "
  event C(200),
  object C(200),
  email C(200)
",

'tiki_menu_options' => "
  option_id I4 AUTO PRIMARY,
  menu_id I4,
  type C(1),
  name C(200),
  url C(255),
  position I4,
  section C(255),
  perm C(255),
  groupname C(255)
",

'tiki_menus' => "
  menu_id I4 AUTO PRIMARY,
  name C(200) NOTNULL,
  description X,
  type C(1)
",

'tiki_module_map' => "
  module_id I4 AUTO PRIMARY,
  module_rsrc C(250) NOTNULL
",

'tiki_pageviews' => "
  day I8 PRIMARY,
  pageviews I8
",

'tiki_preferences' => "
  name C(40) PRIMARY,
  package C(100),
  value C(250)
",

'tiki_programmed_content' => "
  p_id I4 AUTO PRIMARY,
  content_id I4 NOTNULL,
  publish_date I8 NOTNULL,
  data X
",

);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( KERNEL_PKG_NAME, $tableName, $tables[$tableName], TRUE );
}

$indices = array (
	'tiki_layouts_user_id_idx' => array( 'table' => 'tiki_layouts', 'cols' => 'user_id', 'opts' => NULL ),
	'tiki_layouts_layout_idx' => array( 'table' => 'tiki_layouts', 'cols' => 'layout', 'opts' => NULL ),
	'tiki_module_map_rsrc_idx' => array( 'table' => 'tiki_module_map', 'cols' => 'module_rsrc', 'opts' => NULL )
);

$gBitInstaller->registerSchemaIndexes( KERNEL_PKG_NAME, $indices );

$gBitInstaller->registerPackageInfo( KERNEL_PKG_NAME, array(
	'description' => "This is the heart of the application. Without this --&gt; nothing.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '0.1',
	'state' => 'experimental',
	'dependencies' => '',
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( KERNEL_PKG_NAME, array(
	array('kernel','feature_help','y'),
	array('kernel','feature_wikihelp','y'),
	array('kernel','feature_helpnotes','y'),
	array('kernel','short_date_format','%d %b %Y'),
	array('kernel','short_time_format','%H:%M %Z'),
	array('kernel','siteTitle',''),
	array('kernel','centralized_upload_dir','storage'),
	array('kernel','tmpDir','temp'),
	array('kernel','use_proxy','n'),
	array('kernel','proxy_host',''),
	array('kernel','proxy_port',''),
	array('kernel','user_assigned_modules','n'),
	array('kernel','http_domain',''),
	array('kernel','http_port','80'),
	array('kernel','http_prefix','/'),
	array('kernel','https_domain',''),
	array('kernel','https_login','n'),
	array('kernel','https_login_required','n'),
	array('kernel','https_port','443'),
	array('kernel','https_prefix','/'),
	array('kernel','feature_bot_bar','y'),
	array('kernel','feature_top_bar','y'),
	array('kernel','feature_banning','n'),
	array('kernel','feature_contact','n'),
	array('kernel','feature_debug_console','n'),
	array('kernel','feature_debugger_console','n'),
	array('kernel','feature_jstabs','y' ),
	array('kernel','contact_user','admin'),
	array('kernel','count_admin_pvs','y'),
	array('kernel','direct_pagination','n'),
	array('kernel','display_timezone','EST'),
	array('kernel','long_date_format','%A %d of %B, %Y'),
	array('kernel','long_time_format','%H:%M:%S %Z'),
	array('kernel','feature_left_column','y'),
	array('kernel','feature_right_column','y'),
	array('kernel','maxRecords','10'),
	array('kernel','language','en'),
	array('kernel','sender_email',''),
	array('kernel','urlIndex',''),
	array('themes','feature_bidi','n' ),
	array('themes','slide_style', DEFAULT_THEME ),
	array('themes','style', DEFAULT_THEME ),
	array('themes','feature_theme_control','n' ),
	array('themes','feature_top_bar_dropdown','y' )
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
	array('bit_p_admin', 'Can manage users groups and permissions and all aspects of site management', 'admin', 'kernel' ),
	array('bit_p_edit_cookies', 'Can admin cookies', 'editors', 'kernel'),
	array('bit_p_admin_banning', 'Can ban users or IPs', 'admin', 'kernel'),
	array('bit_p_access_closed_site', 'Can access site when closed', 'admin', 'kernel')
) );

?>
