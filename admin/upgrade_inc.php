<?php
global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;
$upgrades = array(

'TIKIWIKI19' => array (
	'TIKIWIKI18' => array (
/*
 leave these in for now - might show up in R2

CREATE TABLE tiki_logs (
  logId int(8) NOT NULL auto_increment,
  logtype varchar(20) NOT NULL,
  logmessage text NOT NULL,
  loguser varchar(200) NOT NULL,
  logip varchar(200) NOT NULL,
  logclient text NOT NULL,
  logtime int(14) NOT NULL,
  PRIMARY KEY  (logId),
  KEY logtype (logtype)
);
CREATE TABLE tiki_secdb(
  md5_value varchar(32) NOT NULL,
  filename varchar(250) NOT NULL,
  tiki_version varchar(60) NOT NULL,
  severity int(4) NOT NULL default '0',
  PRIMARY KEY  (md5_value,filename,tiki_version),
  KEY sdb_fn (filename)
);
ALTER TABLE `tiki_cookies` CHANGE cookie cookie text;
alter table tiki_sessions add tikihost varchar(200) default NULL;
*/

	)
),

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
	'BWR1' => array(
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
		"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` ( `name`, `value` ) VALUES ( 'top_bar_dropdown', 'y' )",
	)
)),

// STEP 3
array( 'DATADICT' => array(
	array( 'DROPCOLUMN' => array(
	)),
)),

	)
),

'BWR1' => array(
	'BWR2' => array(
array( 'DATADICT' => array(
	// RENAME
	array( 'RENAMETABLE' => array(
		'tiki_preferences'       => 'kernel_config',
		'tiki_mail_events'       => 'mail_notifications',
		// TODO: this table is marked for removal
		'tiki_dynamic_variables' => 'liberty_dynamic_variables',
	)),
	array( 'DROPTABLE' => array(
		'tiki_programmed_content', 'tiki_dsn', 'tiki_content_templates', 'tiki_content_templates_sections', 'tiki_menus', 'tiki_menu_options',
	)),
)),
// clean up all kernel_config in the database by using only under_scores in the databae
array( 'QUERY' =>
	array( 'SQL92' => array(
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='bit_index' WHERE `config_name`='bitIndex'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='allow_register' WHERE `config_name`='allowRegister'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='forgot_pass' WHERE `config_name`='forgotPass'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='eponymous_groups' WHERE `config_name`='eponymousGroups'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='register_passcode' WHERE `config_name`='registerPasscode'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='use_register_passcode' WHERE `config_name`='useRegisterPasscode'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='validate_user' WHERE `config_name`='validateUsers'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='validate_email' WHERE `config_name`='validateEmail'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='temp_dir' WHERE `config_name`='tmpDir'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='max_records' WHERE `config_name`='maxRecords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='url_index' WHERE `config_name`='urlIndex'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='anon_can_edit' WHERE `config_name`='anonCanEdit'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='max_versions' WHERE `config_name`='maxVersions'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_home_page' WHERE `config_name`='wikiHomePage'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_license_page' WHERE `config_name`='wikiLicensePage'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_submit_notice' WHERE `config_name`='wikiSubmitNotice'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_title' WHERE `config_name`='siteTitle'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='feature_last_changes' WHERE `config_name`='last_changes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='feature_like_pages' WHERE `config_name`='like_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='feature_list_pages' WHERE `config_name`='list_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='feature_user_preferences' WHERE `config_name`='user_preferences'",

		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='allow_dup_wiki_page_names' WHERE `config_name`='feature_allow_dup_wiki_page_names'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='article_submissions' WHERE `config_name`='feature_article_submissions'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='autolinks' WHERE `config_name`='feature_autolinks'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='babelfish' WHERE `config_name`='feature_babelfish'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='babelfish_logo' WHERE `config_name`='feature_babelfish_logo'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_backlinks' WHERE `config_name`='feature_backlinks'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='banning' WHERE `config_name`='feature_banning'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='bidirectional_text' WHERE `config_name`='feature_bidi'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='blogposts_comments' WHERE `config_name`='feature_blogposts_comments'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='blog_rankings' WHERE `config_name`='feature_blog_rankings'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='bot_bar' WHERE `config_name`='feature_bot_bar'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='clear_passwords' WHERE `config_name`='feature_clear_passwords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='cms_rankings' WHERE `config_name`='feature_cms_rankings'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_contact' WHERE `config_name`='feature_contact'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='custom_home' WHERE `config_name`='feature_custom_home'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_dump' WHERE `config_name`='feature_dump'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_online_help' WHERE `config_name`='feature_help'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_form_help' WHERE `config_name`='feature_helpnotes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_help_popup' WHERE `config_name`='feature_helppopup'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='hotwords' WHERE `config_name`='feature_hotwords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='hotwords_nw' WHERE `config_name`='feature_hotwords_nw'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='jstabs' WHERE `config_name`='feature_jstabs'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='last_changes' WHERE `config_name`='feature_lastChanges'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='left_column' WHERE `config_name`='feature_left_column'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='like_pages' WHERE `config_name`='feature_likePages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='list_pages' WHERE `config_name`='feature_listPages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='page_title' WHERE `config_name`='feature_page_title'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_ranking' WHERE `config_name`='feature_ranking'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='referer_stats' WHERE `config_name`='feature_referer_stats'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='right_column' WHERE `config_name`='feature_right_column'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='sandbox' WHERE `config_name`='feature_sandbox'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='search_fulltext' WHERE `config_name`='feature_search_fulltext'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='search_stats' WHERE `config_name`='feature_search_stats'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='theme_control' WHERE `config_name`='feature_theme_control'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='top_bar' WHERE `config_name`='feature_top_bar'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='top_bar_dropdown' WHERE `config_name`='feature_top_bar_dropdown'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='user_bookmarks' WHERE `config_name`='feature_user_bookmarks'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='user_files' WHERE `config_name`='feature_userfiles'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='usermenu' WHERE `config_name`='feature_usermenu'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='user_preferences' WHERE `config_name`='feature_userPreferences'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='user_watches' WHERE `config_name`='feature_user_watches'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='warn_on_edit' WHERE `config_name`='feature_warn_on_edit'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_attachments' WHERE `config_name`='feature_wiki_attachments'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_books' WHERE `config_name`='feature_wiki_books'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_comments' WHERE `config_name`='feature_wiki_comments'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_description' WHERE `config_name`='feature_wiki_description'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_discuss' WHERE `config_name`='feature_wiki_discuss'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_footnotes' WHERE `config_name`='feature_wiki_footnotes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_generate_pdf' WHERE `config_name`='feature_wiki_generate_pdf'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_edit_help' WHERE `config_name`='feature_wikihelp'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_history' WHERE `config_name`='feature_history'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_icache' WHERE `config_name`='feature_wiki_icache'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_monosp' WHERE `config_name`='feature_wiki_monosp'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_multiprint' WHERE `config_name`='feature_wiki_multiprint'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_notepad' WHERE `config_name`='feature_wiki_notepad'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_pictures' WHERE `config_name`='feature_wiki_pictures'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_plurals' WHERE `config_name`='feature_wiki_plurals'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_rankings' WHERE `config_name`='feature_wiki_rankings'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_tables' WHERE `config_name`='feature_wiki_tables'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_undo' WHERE `config_name`='feature_wiki_undo'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_usrlock' WHERE `config_name`='feature_wiki_usrlock'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_words' WHERE `config_name`='feature_wikiwords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_copyrights' WHERE `config_name`='wiki_feature_copyrights'",

		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pretty_urls_extended' WHERE `config_name`='feature_pretty_urls_extended'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='messages_allow_messages' WHERE `config_name`='allowMsgs'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='users_themes' WHERE `config_name`='feature_user_theme'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='categories_objects' WHERE `config_name`='feature_categoryobjects'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='categories_path' WHERE `config_name`='feature_categorypath'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='liberty_offline_thumbnailer' WHERE `config_name`='feature_offline_thumbnailer'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='kernel_server_name' WHERE `config_name`='feature_server_name'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='output_obzip' WHERE `config_name`='feature_obzip'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='themes_edit_css' WHERE `config_name`='feature_editcss'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='html_pages' WHERE `config_name`='feature_html_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='themes_module_controls' WHERE `config_name`='feature_modulecontrols'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='themes_collapsible_modules' WHERE `config_name`='feature_collapsible_modules'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='kernel_live_support' WHERE `config_name`='feature_live_support'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='themes_dropdown_navbar' WHERE `config_name`='feature_dropdown_navbar'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='users_layouts' WHERE `config_name`='feature_user_layout'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='users_preferences' WHERE `config_name`='feature_user_preferences'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='sample_list_samples' WHERE `config_name`='feature_listSamples'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='stats_search' WHERE `config_name`='feature_search_stats'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_last_changes' WHERE `config_name`='feature_last_changes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_like_pages' WHERE `config_name`='feature_like_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_list_pages' WHERE `config_name`='feature_list_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_user_versions' WHERE `config_name`='feature_userVersions'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_url_import' WHERE `config_name`='feature_wiki_url_import'",

		// added 2006-04-17
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_author' WHERE `config_name`='art_list_author'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_date' WHERE `config_name`='art_list_date'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_img' WHERE `config_name`='art_list_img'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_reads' WHERE `config_name`='art_list_reads'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_size' WHERE `config_name`='art_list_size'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_title' WHERE `config_name`='art_list_title'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_topic' WHERE `config_name`='art_list_topic'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_type' WHERE `config_name`='art_list_type'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_list_expire' WHERE `config_name`='art_list_expire'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_max_list' WHERE `config_name`='max_articles'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_rankings' WHERE `config_name`='cms_rankings'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_submissions' WHERE `config_name`='article_submissions'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_description_length' WHERE `config_name`='article_description_length'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_date_threshold' WHERE `config_name`='article_date_threshold'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='articles_display_filter_bar' WHERE `config_name`='display_article_filter_bar'",

		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_backlinks' WHERE `config_name`='backlinks'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_allow_dup_page_names' WHERE `config_name`='allow_dup_wiki_page_names'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_page_title' WHERE `config_name`='page_title'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_sandbox' WHERE `config_name`='sandbox'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_attachments_use_db' WHERE `config_name`='w_use_db'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_attachments_use_dir' WHERE `config_name`='w_use_dir'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_warn_on_edit_time' WHERE `config_name`='warn_on_edit_time'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_book_show_path' WHERE `config_name`='wikibook_show_path'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_book_show_navigation' WHERE `config_name`='wikibook_show_navigation'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_min_versions' WHERE `config_name`='keep_versions'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='wiki_max_versions' WHERE `config_name`='max_versions'",

		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='messages_site_contact' WHERE `config_name`='site_contact'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='messages_contact_user' WHERE `config_name`='contact_user'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='chatterbox_prune_threshold' WHERE `config_name`='prune_threshold'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='languages_record_untranslated' WHERE `config_name`='record_untranslated'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='hotwords_new_window' WHERE `config_name`='hotwords_nw'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='blog_posts_comments' WHERE `config_name`='blogposts_comments'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pigeonholes_display_members' WHERE `config_name`='display_pigeonhole_members'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pigeonholes_limit_member_number' WHERE `config_name`='limit_member_number'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_session_time' WHERE `config_name`='pgv_session_time'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_calendar_format' WHERE `config_name`='calendar_format'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_default_pedigree_generations' WHERE `config_name`='default_pedigree_generations'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_max_pedigree_generations' WHERE `config_name`='max_pedigree_generations'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_max_descendancy_generations' WHERE `config_name`='max_descendancy_generations'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_use_RIN' WHERE `config_name`='use_RIN'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_pedigree_root_id' WHERE `config_name`='pedigree_root_id'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_gedcom_prefix_id' WHERE `config_name`='gedcom_prefix_id'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_source_prefix_id' WHERE `config_name`='source_prefix_id'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_repo_prefix_id' WHERE `config_name`='repo_prefix_id'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_fam_prefix_id' WHERE `config_name`='fam_prefix_id'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pgv_media_prefix_id' WHERE `config_name`='media_prefix_id'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='stats_referers' WHERE `config_name`='referer_stats'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='liberty_cache_images' WHERE `config_name`='cacheimages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='liberty_cache_pages' WHERE `config_name`='cachepages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='tidbits_banning' WHERE `config_name`='banning'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='tidbits_userfiles' WHERE `config_name`='user_files'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='tidbits_userfiles_use_dir' WHERE `config_name`='uf_use_dir'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='tidbits_tasks' WHERE `config_name`='feature_tasks'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='tidbits_usermenu' WHERE `config_name`='usermenu'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='tidbits_bookmarks' WHERE `config_name`='user_bookmarks'",

		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_slide_style' WHERE `config_name`='slide_style'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_top_bar_dropdown' WHERE `config_name`='top_bar_dropdown'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_show_all_modules_always' WHERE `config_name`='modallgroups'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_disable_jstabs' WHERE `config_name`='disable_jstabs'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_top_bar' WHERE `config_name`='top_bar'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_bot_bar' WHERE `config_name`='bot_bar'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_left_column' WHERE `config_name`='left_column'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_right_column' WHERE `config_name`='right_column'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_biticon_display_style' WHERE `config_name`='biticon_display'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_disable_fat' WHERE `config_name`='disable_fat'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_hide_my_top_bar_link' WHERE `config_name`='hide_my_top_bar_link'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_collapsible_modules' WHERE `config_name`='themes_collapsible_modules'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_module_controls' WHERE `config_name`='themes_module_controls'",

		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='nexus_top_bar' WHERE `config_name`='top_bar_position'",

	//	"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `value`='small' WHERE `config_name`='liberty_auto_display_attachment_thumbs'",
		// added 2006-04-19
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_session_lifetime' WHERE `config_name`='session_lifetime'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_use_load_threshold' WHERE `config_name`='use_load_threshold'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_load_threshold' WHERE `config_name`='load_threshold'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_output_obzip' WHERE `config_name`='output_obzip'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_help_popup' WHERE `config_name`='help_popup'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_store_session_db' WHERE `config_name`='session_db'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_use_proxy' WHERE `config_name`='use_proxy'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_proxy_host' WHERE `config_name`='proxy_host'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_proxy_port' WHERE `config_name`='proxy_port'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_user_assigned_modules' WHERE `config_name`='user_assigned_modules'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_http_domain' WHERE `config_name`='http_domain'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_https_domain' WHERE `config_name`='https_domain'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_https_login' WHERE `config_name`='https_login'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_https_login_required' WHERE `config_name`='https_login_required'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_direct_pagination' WHERE `config_name`='direct_pagination'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_edit_help' WHERE `config_name`='wiki_help'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_form_help' WHERE `config_name`='help_notes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_long_date_format' WHERE `config_name`='long_date_format'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_long_time_format' WHERE `config_name`='long_time_format'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_short_date_format' WHERE `config_name`='short_date_format'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_short_time_format' WHERE `config_name`='short_time_format'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_display_timezone' WHERE `config_name`='display_timezone'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_upload_dir' WHERE `config_name`='centralized_upload_dir'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_temp_dir' WHERE `config_name`='temp_dir'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_http_port' WHERE `config_name`='http_port'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_http_prefix' WHERE `config_name`='http_prefix'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_https_port' WHERE `config_name`='https_port'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_https_prefix' WHERE `config_name`='https_prefix'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_sender_email' WHERE `config_name`='sender_email'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_url_index' WHERE `config_name`='url_index'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='site_online_help' WHERE `config_name`='help'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='users_display_name' WHERE `config_name`='display_name'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_userdn' WHERE `config_name`='users_ldap_userdn'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_memberisdn' WHERE `config_name`='users_ldap_memberisdn'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_groupdn' WHERE `config_name`='users_ldap_groupdn'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_basedn' WHERE `config_name`='users_ldap_basedn'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_adminuser' WHERE `config_name`='users_ldap_adminuser'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_adminpass' WHERE `config_name`='users_ldap_adminpass'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_groupattr' WHERE `config_name`='users_ldap_groupattr'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_groupoc' WHERE `config_name`='users_ldap_groupoc'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_host' WHERE `config_name`='users_ldap_host'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_memberattr' WHERE `config_name`='users_ldap_memberattr'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_port' WHERE `config_name`='users_ldap_port'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_scope' WHERE `config_name`='users_ldap_scope'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_userattr' WHERE `config_name`='users_ldap_userattr'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_ldap_useroc' WHERE `config_name`='users_ldap_useroc'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_method' WHERE `config_name`='users_auth_method'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_skip_admin' WHERE `config_name`='users_auth_skip_admin'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_create_gBitDbUser' WHERE `config_name`='users_auth_create_gBitDbUser'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='auth_create_user_auth' WHERE `config_name`='users_auth_create_user_auth'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='webserverauth' WHERE `config_name`='users_webserverauth'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='count_admin_pvs' WHERE `config_name`='users_count_admin_pageviews'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='allow_register' WHERE `config_name`='users_allow_register'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='forgot_pass' WHERE `config_name`='users_forgot_pass'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pass_due' WHERE `config_name`='users_pass_due'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='rememberme' WHERE `config_name`='users_remember_me'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='remembertime' WHERE `config_name`='users_remember_time'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='userfiles_quota' WHERE `config_name`='users_userfiles_quota'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='uf_use_db' WHERE `config_name`='users_uf_use_db'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='min_pass_length' WHERE `config_name`='users_min_pass_length'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='change_language' WHERE `config_name`='users_change_language'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='case_sensitive_login' WHERE `config_name`='users_case_sensitive_login'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='user_watches' WHERE `config_name`='users_watches'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='custom_home' WHERE `config_name`='users_custom_home'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='clear_passwords' WHERE `config_name`='users_clear_passwords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='validate_email' WHERE `config_name`='users_validate_email'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='validate_user' WHERE `config_name`='users_validate_user'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='use_register_passcode' WHERE `config_name`='users_register_passcode'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='rnd_num_reg' WHERE `config_name`='users_random_number_reg'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='register_passcode' WHERE `config_name`='users_register_passcode'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='pass_chr_num' WHERE `config_name`='users_pass_chr_num'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='eponymous_groups' WHERE `config_name`='users_eponymous_groups'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `config_name`='display_name' WHERE `config_name`='users_display_name'",

	)
)),
array( 'DATADICT' => array(
	array( 'RENAMECOLUMN' => array(
		'kernel_config' => array(
			'`value`' => '`config_value` C(250)',
			'`name`' => '`config_name` C(250)',
		),
	)),
)),
	)
),

);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( KERNEL_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}


/*

-- Calendars
alter table `tiki_calendars` drop `customevent`;
alter table `tiki_calendar_items` drop `evId`;
drop table if exists tiki_event_subscription;
drop table if exists tiki_events;
drop table if exists tiki_sent_events;
ALTER TABLE `tiki_calendar_items` ADD `nlId` INT( 12 ) DEFAULT '0' NOT NULL AFTER `categoryId` ;
ALTER TABLE `tiki_calendars` ADD `customsubscription` ENUM('n','y') DEFAULT 'n' NOT NULL AFTER `customparticipants` ;
ALTER TABLE tiki_calendars ADD personal ENUM ('n', 'y') NOT NULL DEFAULT 'n' AFTER lastmodif;



-- filegals
alter table tiki_files add column search_data longtext;
alter table tiki_files add column lastModif integer(14) DEFAULT NULL;
alter table tiki_files add column lastModifUser varchar(200) DEFAULT NULL;
alter table tiki_files drop KEY ft;
alter table tiki_files add FULLTEXT ft (name, description, search_data);
CREATE TABLE tiki_file_handlers (
	mime_type varchar(64) default NULL,
	cmd varchar(238) default NULL
);



-- forums
ALTER TABLE `tiki_forums` ADD `outbound_mails_for_inbound_mails` CHAR( 1 ) AFTER `outbound_address` ;
ALTER TABLE `tiki_forums` ADD `outbound_mails_reply_link` CHAR( 1 ) AFTER `outbound_mails_for_inbound_mails`;



-- Galaxia
ALTER TABLE galaxia_instances ADD name varchar(200) default 'No Name' NOT NULL AFTER started;
ALTER TABLE galaxia_activities ADD expirationTime int(6) unsigned default 0;



-- imagegals
ALTER TABLE `tiki_galleries` ADD `geographic` char(1) default NULL AFTER `visible`;
ALTER TABLE `tiki_images` ADD `lat` float default NULL AFTER `description`;
ALTER TABLE `tiki_images` ADD `lon` float default NULL AFTER `description`;
ALTER TABLE tiki_galleries ADD COLUMN (
        sortorder VARCHAR(20) NOT NULL DEFAULT 'created',
        sortdirection VARCHAR(4) NOT NULL DEFAULT 'desc',
        galleryimage VARCHAR(20) NOT NULL DEFAULT 'first',
        parentgallery int(14) NOT NULL default -1,
        showname char(1) NOT NULL DEFAULT 'y',
        showimageid char(1) NOT NULL DEFAULT 'n',
        showdescription char(1) NOT NULL DEFAULT 'n',
        showcreated char(1) NOT NULL DEFAULT 'n',
        showuser char(1) NOT NULL DEFAULT 'n',
        showhits char(1) NOT NULL DEFAULT 'y',
        showxysize char(1) NOT NULL DEFAULT 'y',
        showfilesize char(1) NOT NULL DEFAULT 'n',
        showfilename char(1) NOT NULL DEFAULT 'n',
        defaultscale varchar(10) NOT NULL DEFAULT 'o'
);
alter table tiki_galleries_scales add column (scale int(11) NOT NULL default 0);
update tiki_galleries_scales set scale=greatest(xsize,ysize);
alter table tiki_galleries_scales drop primary key;
alter table tiki_galleries_scales drop column xsize;
alter table tiki_galleries_scales drop column ysize;
alter table tiki_galleries_scales add primary key (galleryId,scale);
ALTER TABLE `tiki_images_data` ADD `etag` varchar(32) default NULL;



-- Karma
CREATE TABLE tiki_score (
  event varchar(40) NOT NULL default '',
  score int(11) NOT NULL default '0',
  expiration int(11) NOT NULL default '0',
  category text NOT NULL,
  description text NOT NULL,
  ord int(11) NOT NULL default '0',
  PRIMARY KEY  (event),
  KEY ord (ord)
);
CREATE TABLE users_score (
  user char(40) NOT NULL default '',
  event_id char(40) NOT NULL default '',
  score int(11) NOT NULL default '0',
  expire datetime NOT NULL default '0000-00-00 00:00:00',
  tstamp timestamp(14) NOT NULL,
  PRIMARY KEY  (user,event_id),
  KEY user (user,event_id,expire)
);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('login',1,0,'General','Login',1);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('login_remain',2,60,'General','Stay logged',2);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('profile_fill',10,0,'General','Fill each profile field',3);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('profile_see',2,0,'General','See other user''s profile',4);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('profile_is_seen',1,0,'General','Have your profile seen',5);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('friend_new',10,0,'General','Make friends (feature not available yet)',6);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('message_receive',1,0,'General','Receive message',7);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('message_send',2,0,'General','Send message',8);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_read',2,0,'Articles','Read an article',9);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_comment',5,0,'Articles','Comment an article',10);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_new',20,0,'Articles','Publish an article',11);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_is_read',1,0,'Articles','Have your article read',12);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_is_commented',2,0,'Articles','Have your article commented',13);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('fgallery_new',10,0,'File galleries','Create new file gallery',14);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('fgallery_new_file',10,0,'File galleries','Upload new file to gallery',15);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('fgallery_download',5,0,'File galleries','Download other user''s file',16);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('fgallery_is_downloaded',5,0,'File galleries','Have your file downloaded',17);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('igallery_new',10,0,'Image galleries','Create a new image gallery',18);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('igallery_new_img',6,0,'Image galleries','Upload new image to gallery',19);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('igallery_see_img',3,0,'Image galleries','See other user''s image',20);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('igallery_img_seen',1,0,'Image galleries','Have your image seen',21);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_new',20,0,'Blogs','Create new blog',22);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_post',5,0,'Blogs','Post in a blog',23);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_read',2,0,'Blogs','Read other user''s blog',24);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_comment',2,0,'Blogs','Comment other user''s blog',25);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_is_read',3,0,'Blogs','Have your blog read',26);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_is_commented',3,0,'Blogs','Have your blog commented',27);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('wiki_new',10,0,'Wiki','Create a new wiki page',28);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('wiki_edit',5,0,'Wiki','Edit an existing page',29);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('wiki_attach_file',3,0,'Wiki','Attach file',30);
CREATE TABLE tiki_friends (
  user char(40) NOT NULL default '',
  friend char(40) NOT NULL default '',
  PRIMARY KEY  (user,friend)
);

CREATE TABLE tiki_friendship_requests (
  userFrom char(40) NOT NULL default '',
  userTo char(40) NOT NULL default '',
  tstamp timestamp(14) NOT NULL,
  PRIMARY KEY  (userFrom,userTo)
);
ALTER TABLE users_score RENAME TO tiki_users_score;
ALTER TABLE users_users ADD score int4 NOT NULL default 0;
ALTER TABLE `users_users` CHANGE score score int(11) NOT NULL default '0';
alter table `tiki_users_score` modify `expire` int(14) not null;
alter table `tiki_score` drop description;
alter table `tiki_score` drop category;
alter table `tiki_score` drop ord;
alter table `tiki_users_score` drop score;



-- Languages
ALTER TABLE `tiki_language` CHANGE `lang` `lang` char(16) NOT NULL default '';
ALTER TABLE `tiki_languages` CHANGE `lang` `lang` char(16) NOT NULL default '';
ALTER TABLE `tiki_calendar_items` CHANGE `lang` `lang` char(16) NOT NULL default 'en';
ALTER TABLE `tiki_menu_languages` CHANGE `language` `language` char(16) NOT NULL default '';
ALTER TABLE `tiki_untranslated` CHANGE `lang` `lang` char(16) NOT NULL default '';



-- Liberty
CREATE TABLE tiki_translated_objects (
  traId int(14) NOT NULL auto_increment,
  type varchar(50) NOT NULL,
  objId varchar(255) NOT NULL,
  lang varchar(16) default NULL,
  PRIMARY KEY (type, objId),
  KEY ( traId)
) AUTO_INCREMENT=1;
CREATE TABLE tiki_structure_versions (
  structure_id int(14) NOT NULL auto_increment,
  version int(14) default NULL,
  PRIMARY KEY  (structure_id)
) AUTO_INCREMENT=1 ;

ALTER TABLE `tiki_structures` ADD `structure_id` int(14) NOT NULL AFTER `page_ref_id`;
ALTER TABLE `tiki_structures` ADD `page_version` int(8) default NULL AFTER `page_id`;



-- Mailin
ALTER TABLE tiki_mailin_accounts ADD anonymous char(1) NOT NULL default 'y';
ALTER TABLE `tiki_mailin_accounts` ADD `article_topicId` int(4) DEFAULT NULL , ADD `article_type` varchar(50) DEFAULT NULL;
ALTER TABLE tiki_mailin_accounts add column (discard_after varchar(255) default NULL);



-- messages
CREATE TABLE messages_archive (
  msgId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  user_from varchar(200) NOT NULL default '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) default NULL,
  body text,
  hash varchar(32) default NULL,
  date int(14) default NULL,
  isRead char(1) default NULL,
  isReplied char(1) default NULL,
  isFlagged char(1) default NULL,
  priority int(2) default NULL,
  PRIMARY KEY  (msgId)
) AUTO_INCREMENT=1 ;
CREATE TABLE messages_sent (
  msgId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  user_from varchar(200) NOT NULL default '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) default NULL,
  body text,
  hash varchar(32) default NULL,
  date int(14) default NULL,
  isRead char(1) default NULL,
  isReplied char(1) default NULL,
  isFlagged char(1) default NULL,
  priority int(2) default NULL,
  PRIMARY KEY  (msgId)
) AUTO_INCREMENT=1 ;

ALTER TABLE messages_messages ADD replyto_hash varchar(32) default NULL AFTER hash;
ALTER TABLE messages_archive ADD replyto_hash varchar(32) default NULL AFTER hash;
ALTER TABLE messages_sent ADD replyto_hash varchar(32) default NULL AFTER hash;



-- newsletters
CREATE TABLE tiki_newsletter_groups (
  nlId int(12) NOT NULL default '0',
  groupName varchar(255) NOT NULL default '',
  code varchar(20),
  PRIMARY KEY  (nlId,groupName)
);
ALTER TABLE tiki_newsletter_subscriptions ADD isUser char(1) NOT NULL default 'n' AFTER subscribed;
ALTER  TABLE  tiki_newsletter_groups  modify  code varchar(32) default NULL;



-- Polls
CREATE TABLE `tiki_poll_objects` (
  `catObjectId` int(11) NOT NULL default '0',
  `pollId` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`catObjectId`,`pollId`)
);
ALTER TABLE `tiki_poll_options` ADD `position` INT( 4 ) DEFAULT '0' NOT NULL AFTER `title` ;
CREATE TABLE `tiki_object_ratings` (
  `catObjectId` int(12) NOT NULL default '0',
  `pollId` int(12) NOT NULL default '0',
  PRIMARY KEY  (`catObjectId`,`pollId`)
);



-- Quicktags
ALTER TABLE `tiki_quicktags` ADD `tagcategory` CHAR( 255 ) AFTER `tagicon` ;



-- Quizzes
ALTER TABLE `tiki_quizzes` ADD `immediateFeedback` char(1) default NULL ;
ALTER TABLE `tiki_quizzes` ADD `showAnswers` char(1) default NULL ;
ALTER TABLE `tiki_quizzes` ADD `shuffleQuestions` char(1) default NULL ;
ALTER TABLE `tiki_quizzes` ADD `shuffleAnswers` char(1) default NULL ;
ALTER TABLE `tiki_quizzes` ADD `publishDate` int(14) default NULL;
ALTER TABLE `tiki_quizzes` ADD `expireDate` int(14) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bDeleted` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `nVersion` int(4) NOT NULL ;
ALTER TABLE `tiki_quizzes` ADD `nAuthor` int(4) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bOnline` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bRandomQuestions` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `nRandomQuestions` tinyint(4) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bLimitQuestionsPerPage` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `nLimitQuestionsPerPage` tinyint(4) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bMultiSession` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `nCanRepeat` tinyint(4) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sGradingMethod` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sShowScore` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sShowCorrectAnswers` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sPublishStats` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bAdditionalQuestions` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bForum` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sForum` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sPrologue` text default NULL;
ALTER TABLE `tiki_quizzes` ADD `sData` text default NULL;
ALTER TABLE `tiki_quizzes` ADD `sEpilogue` text default NULL;
ALTER TABLE `tiki_quizzes` ADD `passingperct` int(4) default 0;
CREATE TABLE `tiki_user_answers_uploads` (
  `answerUploadId` int(4) NOT NULL auto_increment,
  `userResultId` int(11) NOT NULL default '0',
  `questionId` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filetype` varchar(64) NOT NULL default '',
  `filesize` varchar(255) NOT NULL default '',
  `filecontent` longblob NOT NULL,
  PRIMARY KEY  (`answerUploadId`)
);



-- Search
CREATE TABLE IF NOT EXISTS tiki_searchsyllable(
  syllable varchar(80) NOT NULL default '',
  lastUsed int(11) NOT NULL default '0',
  lastUpdated int(11) NOT NULL default '0',
  PRIMARY KEY  (syllable),
  KEY lastUsed (lastUsed)
);
CREATE TABLE  IF NOT EXISTS tiki_searchwords(
  syllable varchar(80) NOT NULL default '',
  searchword varchar(80) NOT NULL default '',
  PRIMARY KEY  (syllable,searchword)
);



--Shoutbox
CREATE TABLE `tiki_shoutbox_words` (
  word VARCHAR( 40 ) NOT NULL ,
  qty INT DEFAULT '0' NOT NULL ,
  PRIMARY KEY ( `word` )
);



-- stats
CREATE TABLE `tiki_stats` (
  `object` varchar(255) NOT NULL default '',
  `type` varchar(20) NOT NULL default '',
  `day` int(14) NOT NULL default '0',
  `hits` int(14) NOT NULL default '0',
  PRIMARY KEY  (`object`,`type`,`day`)
);
alter table tiki_referer_stats change referer referer varchar(255) not null;
CREATE TABLE `tiki_stats` (
  `object` varchar(255) NOT NULL default '',
  `type` varchar(20) NOT NULL default '',
  `day` int(14) NOT NULL default '0',
  `hits` int(14) NOT NULL default '0',
  PRIMARY KEY  (`object`,`type`,`day`)
);
ALTER TABLE `users_users` CHANGE score score int(11) NOT NULL default '0';



-- Storage
CREATE TABLE `tiki_download` (
  `id` int(11) NOT NULL auto_increment,
  `object` varchar(255) NOT NULL default '',
  `userId` int(8) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `date` int(14) NOT NULL default '0',
  `IP` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `object` (`object`,`userId`,`type`),
  KEY `userId` (`userId`),
  KEY `type` (`type`),
  KEY `date` (`date`)
) ;




-- TikiSheets
CREATE TABLE tiki_sheet_values (
  sheetId int(8) NOT NULL default '0',
  begin int(10) NOT NULL default '0',
  end int(10) default NULL,
  rowIndex int(4) NOT NULL default '0',
  columnIndex int(4) NOT NULL default '0',
  value varchar(255) default NULL,
  calculation varchar(255) default NULL,
  width int(4) NOT NULL default '1',
  height int(4) NOT NULL default '1',
  UNIQUE KEY sheetId (sheetId,begin,rowIndex,columnIndex),
  KEY sheetId_2 (sheetId,rowIndex,columnIndex)
);
CREATE TABLE tiki_sheets (
  sheetId int(8) NOT NULL auto_increment,
  title varchar(200) NOT NULL default '',
  description text,
  author varchar(200) NOT NULL default '',
  PRIMARY KEY  (sheetId)
);
CREATE TABLE tiki_sheet_layout (
  sheetId int(8) NOT NULL default '0',
  begin int(10) NOT NULL default '0',
  end int(10) default NULL,
  headerRow int(4) NOT NULL default '0',
  footerRow int(4) NOT NULL default '0',
  className varchar(64) default NULL,
  UNIQUE KEY sheetId (sheetId,begin)
);
ALTER TABLE tiki_sheet_values ADD format varchar(255) default NULL;



-- Trackers
ALTER TABLE tiki_tracker_fields ADD position int(4) default NULL;
ALTER TABLE tiki_tracker_item_attachments CHANGE itemId itemId int(12) NOT NULL default 0;
ALTER TABLE tiki_tracker_item_attachments ADD longdesc blob;
ALTER TABLE tiki_tracker_item_attachments ADD version varchar(40) default NULL;
ALTER TABLE tiki_trackers ADD showComments char(1) default NULL;
ALTER TABLE tiki_trackers ADD orderAttachments varchar(255) NOT NULL default 'filename,created,filesize,downloads,desc';
ALTER TABLE `tiki_tracker_fields` CHANGE `name` `name` VARCHAR( 255 ) DEFAULT NULL ;
ALTER TABLE `tiki_tracker_fields` ADD `isSearchable` CHAR(1) NOT NULL default 'y';
ALTER TABLE tiki_tracker_fields ADD isPublic varchar ( 1 ) default NULL;
ALTER TABLE `tiki_tracker_fields` CHANGE `isPublic` `isPublic` CHAR( 1 ) DEFAULT 'y' NOT NULL ;
UPDATE `tiki_tracker_fields` set `isPublic`='y' where `isPublic`='';
ALTER TABLE `tiki_tracker_fields` CHANGE `isPublic` `isPublic` CHAR( 1 ) DEFAULT 'n' NOT NULL ;
ALTER TABLE `tiki_tracker_fields` ADD `isHidden` varchar ( 1 ) DEFAULT 'n' NOT NULL ;
UPDATE `tiki_tracker_fields` set `isHidden`='y' where `isHidden`='';
ALTER TABLE `tiki_trackers` CHANGE `name` `name` VARCHAR( 255 ) DEFAULT NULL ;
CREATE TABLE tiki_tracker_options (
  trackerId int(12) NOT NULL default '0',
  name varchar(80) NOT NULL default '',
  value text default NULL,
  PRIMARY KEY (trackerId,name(30))
) ;
ALTER TABLE `tiki_tracker_fields` ADD `isMandatory` varchar ( 1 ) DEFAULT 'n' NOT NULL ;
UPDATE `tiki_tracker_fields` set `isMandatory`='y' where `isMandatory`='';
ALTER TABLE `tiki_user_votings` ADD optionId int(10) NOT NULL default '0';
ALTER TABLE `users_groups` ADD `usersTrackerId` INT(11) ;
ALTER TABLE `users_groups` ADD `groupTrackerId` INT(11) ;
ALTER TABLE `users_groups` ADD `usersFieldId` INT( 11 ), ADD `groupFieldId` INT( 11 );



*/



?>
