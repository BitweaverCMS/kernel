<?php
global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;
$upgrades = array(

'TIKIWIKI18' => array(
	'BONNIE' => array(
// Step 1
array( 'DATADICT' => array(
array( 'CREATE' => array (
'adodb_logsql' => "
  created T NOT NULL,
  sql0 C(250) NOTNULL,
  sql1 X NOTNULL,
  params X NOTNULL,
  tracer X NOTNULL,
  timer N(16.6) NOTNULL
",
'tiki_module_map' => "
  moduleId I4 AUTO PRIMARY,
  module_rsrc C(250) NOTNULL
",
'tiki_layouts' => "
  userId I4 NOTNULL,
  moduleId I4 NOTNULL,
  layout C(160) NOTNULL DEFAULT 'home',
  position C(1) NOTNULL,
  rows I4,
  params C(255),
  ord I4 NOTNULL DEFAULT '1'
",
'tiki_layouts_modules' => "
  moduleId I4 PRIMARY,
  availability C(1),
  title C(255),
  cache_time I8,
  rows I4,
  params C(255),
  groups X
"
)),
)),

// STEP 2
array( 'QUERY' =>
	array( 'SQL92' => array(
"INSERT INTO `".BIT_DB_PREFIX."tiki_module_map` ( `module_rsrc` ) VALUES ( 'bitpackage:users/mod_login_box.tpl' )",
"INSERT INTO `".BIT_DB_PREFIX."tiki_module_map` ( `module_rsrc` ) VALUES ( 'bitpackage:kernel/mod_application_menu.tpl' )",
"INSERT INTO `".BIT_DB_PREFIX."tiki_layouts_modules` ( `moduleId`, `availability`, `title`, `cache_time`, `rows`, `params`, `groups` ) VALUES ( 1, NULL, 'Login Box', NULL, NULL, NULL, '-1' )",
"INSERT INTO `".BIT_DB_PREFIX."tiki_layouts_modules` ( `moduleId`, `availability`, `title`, `cache_time`, `rows`, `params`, `groups` ) VALUES ( 2, NULL, NULL, NULL, NULL, NULL, '-1' )",
"INSERT INTO `".BIT_DB_PREFIX."tiki_layouts` (`userId`, `moduleId`, `position`, `ord`, `layout`) VALUES (1, 1, 'r', 1, 'kernel')",
"INSERT INTO `".BIT_DB_PREFIX."tiki_layouts` (`userId`, `moduleId`, `position`, `ord`, `layout`) VALUES (1, 2, 'l', 1, 'kernel')",
"DELETE FROM `".BIT_DB_PREFIX."tiki_preferences` WHERE `value` LIKE 'tiki-%.php%'",
)),
),

// STEP 3
array( 'INDEXES' => array (
'tiki_layouts_user_id_idx' => array( 'table' => 'tiki_layouts', 'cols' => 'user_id', 'opts' => NULL ),
'tiki_layouts_layout_idx' => array( 'table' => 'tiki_layouts', 'cols' => 'layout', 'opts' => NULL ),
'tiki_module_map_rsrc_idx' => array( 'table' => 'tiki_module_map', 'cols' => 'module_rsrc', 'opts' => NULL )
))
	),
),

'BONNIE' => array(
	'CLYDE' => array(
array( 'RENAMETABLE' => array(
		'tiki_modules' => 'tiki_layout_modules',
)),
// STEP 1
array( 'DATADICT' => array(
array( 'RENAMECOLUMN' => array(
	'tiki_layouts_modules' => array( '`moduleId`' => '`module_id` I4'	),
	'tiki_module_map' => array( '`moduleId`' => '`module_id` I4 AUTO' ),
	'tiki_layouts' => array( '`userId`' => '`user_id` I4',
							 '`moduleId`' => '`module_id` I4' ),
	'tiki_banning' => array( '`banId`' => '`ban_id` I4 AUTO' ),
	'tiki_banning_sections' => array( '`banId`' => '`ban_id` I4' ),
	'tiki_content_templates' => array( '`templateId`' => '`template_id` I4 AUTO' ),
	'tiki_content_templates_sections' => array( '`templateId`' => '`template_id` I4' ),
	'tiki_cookies' => array( '`cookieId`' => '`cookie_id` I4 AUTO' ),
	'tiki_dsn' => array( '`dsnId`' => '`dsn_id` I4 AUTO' ),
	'tiki_menu_options' => array( '`optionId`' => '`option_id` I4 AUTO',
								  '`menuId`' => '`menu_id` I4' ),
	'tiki_menus' => array( '`menuId`' => '`menu_id` I4 AUTO' ),
	'tiki_programmed_content' => array(	'`pId`' => '`p_id` I4 AUTO',
										'`contentId`' => '`content_id` I4',
										'`publishDate`' => '`publish_date` I8' ),
// Appears to be giving errors - wolff_borg 20050812
//	'sessions' => array( '`data`' => '`session_data` X'	),
)),

array( 'ALTER' => array(
	'tiki_preferences' => array(
		'package' => array( '`package`', 'VARCHAR(100)' ), // , 'NOTNULL' ),
	),
	'users_groups' => array(
		'is_default' => array( '`is_default`', 'VARCHAR(1)' ),
	),
)),
)),

// STEP 2
array( 'QUERY' =>
	array( 'SQL92' => array(
		"UPDATE `".BIT_DB_PREFIX."tiki_module_map` SET `module_rsrc` = replace( `module_rsrc`, 'tikipackage', 'bitpackage' )",
		"UPDATE `".BIT_DB_PREFIX."tiki_preferences` SET `value`='native' WHERE `name`='style'",
		"DELETE FROM `".BIT_DB_PREFIX."tiki_preferences` WHERE `name`='tikiIndex'",
		"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` ( `name`, `value` ) VALUES ( 'bitIndex', 'wiki' )",
		"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` ( `name`, `value` ) VALUES ( 'feature_top_bar_dropdown', 'y' )",
	)
)),

// STEP 3
array( 'DATADICT' => array(
	array( 'DROPCOLUMN' => array(
	)),
)),

	)
)

);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( KERNEL_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}


?>
