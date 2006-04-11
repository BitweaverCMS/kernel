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
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='bit_index' WHERE `name`='bitIndex'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='allow_register' WHERE `name`='allowRegister'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='forgot_pass' WHERE `name`='forgotPass'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='eponymous_groups' WHERE `name`='eponymousGroups'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='register_passcode' WHERE `name`='registerPasscode'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='use_register_passcode' WHERE `name`='useRegisterPasscode'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='validate_user' WHERE `name`='validateUsers'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='validate_email' WHERE `name`='validateEmail'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='temp_dir' WHERE `name`='tmpDir'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='max_records' WHERE `name`='maxRecords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='url_index' WHERE `name`='urlIndex'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='anon_can_edit' WHERE `name`='anonCanEdit'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='max_versions' WHERE `name`='maxVersions'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_home_page' WHERE `name`='wikiHomePage'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_license_page' WHERE `name`='wikiLicensePage'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_submit_notice' WHERE `name`='wikiSubmitNotice'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='site_title' WHERE `name`='siteTitle'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='feature_last_changes' WHERE `name`='last_changes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='feature_like_pages' WHERE `name`='like_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='feature_list_pages' WHERE `name`='list_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='feature_user_preferences' WHERE `name`='user_preferences'",

		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='allow_dup_wiki_page_names' WHERE `name`='feature_allow_dup_wiki_page_names'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='article_submissions' WHERE `name`='feature_article_submissions'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='autolinks' WHERE `name`='feature_autolinks'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='babelfish' WHERE `name`='feature_babelfish'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='babelfish_logo' WHERE `name`='feature_babelfish_logo'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='backlinks' WHERE `name`='feature_backlinks'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='banning' WHERE `name`='feature_banning'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='bidirectional_text' WHERE `name`='feature_bidi'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='blogposts_comments' WHERE `name`='feature_blogposts_comments'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='blog_rankings' WHERE `name`='feature_blog_rankings'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='bot_bar' WHERE `name`='feature_bot_bar'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='clear_passwords' WHERE `name`='feature_clear_passwords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='cms_rankings' WHERE `name`='feature_cms_rankings'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='site_contact' WHERE `name`='feature_contact'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='custom_home' WHERE `name`='feature_custom_home'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_dump' WHERE `name`='feature_dump'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='help' WHERE `name`='feature_help'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='help_notes' WHERE `name`='feature_helpnotes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='help_popup' WHERE `name`='feature_helppopup'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='hotwords' WHERE `name`='feature_hotwords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='hotwords_nw' WHERE `name`='feature_hotwords_nw'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='jstabs' WHERE `name`='feature_jstabs'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='last_changes' WHERE `name`='feature_lastChanges'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='left_column' WHERE `name`='feature_left_column'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='like_pages' WHERE `name`='feature_likePages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='list_pages' WHERE `name`='feature_listPages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='page_title' WHERE `name`='feature_page_title'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_ranking' WHERE `name`='feature_ranking'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='referer_stats' WHERE `name`='feature_referer_stats'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='right_column' WHERE `name`='feature_right_column'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='sandbox' WHERE `name`='feature_sandbox'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='search_fulltext' WHERE `name`='feature_search_fulltext'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='search_stats' WHERE `name`='feature_search_stats'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='theme_control' WHERE `name`='feature_theme_control'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='top_bar' WHERE `name`='feature_top_bar'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='top_bar_dropdown' WHERE `name`='feature_top_bar_dropdown'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='user_bookmarks' WHERE `name`='feature_user_bookmarks'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='user_files' WHERE `name`='feature_userfiles'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='usermenu' WHERE `name`='feature_usermenu'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='user_preferences' WHERE `name`='feature_userPreferences'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='user_watches' WHERE `name`='feature_user_watches'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='warn_on_edit' WHERE `name`='feature_warn_on_edit'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_attachments' WHERE `name`='feature_wiki_attachments'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_books' WHERE `name`='feature_wiki_books'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_comments' WHERE `name`='feature_wiki_comments'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_description' WHERE `name`='feature_wiki_description'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_discuss' WHERE `name`='feature_wiki_discuss'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_footnotes' WHERE `name`='feature_wiki_footnotes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_generate_pdf' WHERE `name`='feature_wiki_generate_pdf'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_help' WHERE `name`='feature_wikihelp'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_history' WHERE `name`='feature_history'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_icache' WHERE `name`='feature_wiki_icache'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_monosp' WHERE `name`='feature_wiki_monosp'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_multiprint' WHERE `name`='feature_wiki_multiprint'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_notepad' WHERE `name`='feature_wiki_notepad'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_pictures' WHERE `name`='feature_wiki_pictures'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_plurals' WHERE `name`='feature_wiki_plurals'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_rankings' WHERE `name`='feature_wiki_rankings'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_tables' WHERE `name`='feature_wiki_tables'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_undo' WHERE `name`='feature_wiki_undo'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_usrlock' WHERE `name`='feature_wiki_usrlock'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_words' WHERE `name`='feature_wikiwords'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_copyrights' WHERE `name`='wiki_feature_copyrights'",

		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='pretty_urls_extended' WHERE `name`='feature_pretty_urls_extended'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='messages_allow_messages' WHERE `name`='allowMsgs'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='users_themes' WHERE `name`='feature_user_theme'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='categories_objects' WHERE `name`='feature_categoryobjects'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='categories_path' WHERE `name`='feature_categorypath'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='liberty_offline_thumbnailer' WHERE `name`='feature_offline_thumbnailer'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='kernel_server_name' WHERE `name`='feature_server_name'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='output_obzip' WHERE `name`='feature_obzip'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='themes_edit_css' WHERE `name`='feature_editcss'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='html_pages' WHERE `name`='feature_html_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='themes_module_controls' WHERE `name`='feature_modulecontrols'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='themes_collapsible_modules' WHERE `name`='feature_collapsible_modules'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='kernel_live_support' WHERE `name`='feature_live_support'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='themes_dropdown_navbar' WHERE `name`='feature_dropdown_navbar'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='users_layouts' WHERE `name`='feature_user_layout'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='users_preferences' WHERE `name`='feature_user_preferences'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='sample_list_samples' WHERE `name`='feature_listSamples'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='stats_search' WHERE `name`='feature_search_stats'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_last_changes' WHERE `name`='feature_last_changes'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_like_pages' WHERE `name`='feature_like_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_list_pages' WHERE `name`='feature_list_pages'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_user_versions' WHERE `name`='feature_userVersions'",
		"UPDATE `".BIT_DB_PREFIX."kernel_config` SET `name`='wiki_url_import' WHERE `name`='feature_wiki_url_import'",
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
