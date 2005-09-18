<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_features_inc.php,v 1.1.1.1.2.6 2005/09/18 12:14:09 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//This doen't scale very well when you have 1000's of users
//$users_list = $gBitUser->get_users_names();
//$gBitSmarty->assign( 'users_list',$users_list );

$formFeaturesTiki = array(
	'pretty_urls' => array(
		'label' => 'Use Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. Using this feature requires Apache <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a> support in the web server (usually installed by default), Depending on the Web server configuration, it may be necessary to modify the default .htaccess files when using this feature.',
		'page' => 'PrettyUrls',
	),
	'feature_pretty_urls_extended' => array(
		'label' => 'Use Extended Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. Using this feature requires Apache <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a> support in the web server (usually installed by default), This extended version adds a /view tag to the URLs to make them unambigious for rewrites.  It will be necessary to modify the default .htaccess files when using this feature.',
		'page' => 'PrettyUrlsExtended',
	),
	'feature_autolinks' => array(
		'label' => 'AutoLinks',
		'note' => 'If enabled, URLs entered by users will automatically be shown as clickable links.',
		'page' => 'AutoLinks',
	),
	'feature_html_pages' => array(
		'label' => 'HTML Pages',
		'note' => 'A simple way for creating pages that will be displayed to the users and that can be linked from/to any place in your site.',
		'page' => 'HtmlPages',
	),
	'feature_categoryobjects' => array(
		'label' => 'Show Category Objects',
		'note' => 'Display a list of items that are part of a particular category at the bottom of the page.',
		'page' => 'ShowCategoryObjects',
	),
	'feature_categorypath' => array(
		'label' => 'Show Category Path',
		'note' => 'Display the category path at the top of the page',
		'page' => 'ShowCategoryPath',
	),
);
$gBitSmarty->assign( 'formFeaturesTiki',$formFeaturesTiki );

$formFeaturesHelp = array(
	'feature_helpnotes' => array(
		'label' => 'Help Notes',
		'note' => 'Show inline help notes in forms such as the one you are reading now.',
		'page' => 'HelpSystem',
	),
	'feature_help' => array(
		'label' => 'Online Help Links',
		'note' => 'Display links to relevant online help pages found on www.bitweaver.org.',
		'page' => 'HelpSystem',
	),
	'feature_wikihelp' => array(
		'label' => 'Show Wiki Help',
		'note' => 'Displays <strong>extensive</strong> (about 50kb) help regarding tikiwiki syntax and wiki plugins whenever there is an entry form that takes wiki syntax.',
		'page' => 'HelpSystem',
	),
	'feature_helppopup' => array(
		'label' => 'Use Popup for Help Items',
		'note' => 'This will place a clickable icon after form elements for the extended help information.',
		'page' => 'HelpSystem',
	),
);
$gBitSmarty->assign( 'formFeaturesHelp',$formFeaturesHelp );

$formFeaturesContent = array(
	'feature_dynamic_content' => array(
		'label' => 'Dynamic Content System',
		'note' => 'You can edit blocks of HTML code or text from a admin screen and you can display this block in any Tiki template or user module. Updating the block content will update the template.',
		'page' => 'DynamicContentSystem',
	),
	'feature_editcss' => array(
		'label' => 'Edit Css',
		'note' => 'Enables you to edit CSS files - this setting is antiquated and will be removed soon. use the stylist package if you want to modify css files.',
		'page' => 'EditCss',
	),
);
$gBitSmarty->assign( 'formFeaturesContent',$formFeaturesContent );

$formFeaturesAdmin = array(
	'feature_banning' => array(
		'label' => 'Banning System',
		'note' => 'This enables you to ban users from this site, based on IP address',
		'page' => 'BanningSystem',
	),
	'feature_referer_stats' => array(
		'label' => 'Referer Statistics',
		'note' => 'Records statistics including HTTP_REFERRER',
		'page' => 'RefererStats',
	),
	'feature_theme_control' => array(
		'label' => 'Theme Control',
		'note' => '???',
		'page' => 'ThemeControl',
	),
);

$gBitSmarty->assign( 'formFeaturesAdmin',$formFeaturesAdmin );

$users_list = $gBitUser->get_users_names();
$gBitSmarty->assign( 'users_list', ( count( $users_list ) < 50 ) ? $users_list : NULL );

$processForm = set_tab();

if( $processForm ) {
	$featureToggles = array_merge( $formFeaturesTiki,$formFeaturesContent,$formFeaturesAdmin,$formFeaturesHelp,array( 'feature_contact' => 0 ) );
	foreach( array_keys( $featureToggles ) as $item ) {
		simple_set_toggle( $item );
	}
	simple_set_value( "contact_user" );
}

$gBitSystem->setHelpInfo('Features','Settings','Help with the features settings');


?>
