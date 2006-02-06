<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_features_inc.php,v 1.10 2006/02/06 22:56:46 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//This doen't scale very well when you have 1000's of users
//$users_list = $gBitUser->get_users_names();
//$gBitSmarty->assign( 'users_list',$users_list );

$formFeaturesBit = array(
	'pretty_urls' => array(
		'label' => 'Use Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. Using this feature requires Apache <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a> support in the web server (usually installed by default), Depending on the Web server configuration, it may be necessary to modify the default .htaccess files when using this feature.',
		'page' => 'PrettyUrls',
	),
	'feature_pretty_urls_extended' => array(
		'label' => 'Use Extended Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. Using this feature requires Apache <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a> support in the web server (usually installed by default), This extended version adds a /view tag to the URLs to make them unambigious for rewrites.  It will be necessary to modify the default .htaccess files when using this feature.',
		'page' => 'FeaturePrettyUrlsExtended',
	),
	'autolinks' => array(
		'label' => 'AutoLinks',
		'note' => 'If enabled, URLs entered by users will automatically be shown as clickable links.',
	),
	'feature_html_pages' => array(
		'label' => 'HTML Pages',
		'note' => 'A simple way for creating pages that will be displayed to the users and that can be linked from/to any place in your site.',
	),
	'feature_jscalendar' => array(
		'label' => 'Enable JSCalendar',
		'note' => 'Enable use of the JSCalendar library',
		'page' => 'JSCalendar',
	),
	'feature_editcss' => array(
		'label' => 'Edit Css',
		'note' => 'Enables you to edit CSS files from within your browser to customise your site style according to your desires.',
	),
	'feature_categoryobjects' => array(
		'label' => 'Show Category Objects',
		'note' => 'Display a list of items that are part of a particular category at the bottom of the page.',
	),
	'feature_categorypath' => array(
		'label' => 'Show Category Path',
		'note' => 'Display the category path at the top of the page',
	),
);
if( $gBitSystem->isPackageActive( 'stats' ) ) {
	$formFeaturesBit['referer_stats'] = array(
		'label' => 'Referer Statistics',
		'note' => 'Records statistics including HTTP_REFERRER',
	);
}
$gBitSmarty->assign( 'formFeaturesBit',$formFeaturesBit );

$formFeaturesHelp = array(
	'help_notes' => array(
		'label' => 'Help Notes',
		'note' => 'Show inline help notes in forms such as the one you are reading now.',
	),
	'help' => array(
		'label' => 'Online Help Links',
		'note' => 'Display links to relevant online help pages found on www.bitweaver.org.',
	),
	'wiki_help' => array(
		'label' => 'Show Wiki Help',
		'note' => 'Displays <strong>extensive</strong> (about 50kb) help regarding tikiwiki syntax and wiki plugins whenever there is an entry form that takes wiki syntax.',
	),
	'help_popup' => array(
		'label' => 'Use Popup for Help Items',
		'note' => 'This will place a clickable icon after form elements for the extended help information.',
	),
);
$gBitSmarty->assign( 'formFeaturesHelp',$formFeaturesHelp );

$users_list = $gBitUser->get_users_names();
$gBitSmarty->assign( 'users_list', ( count( $users_list ) < 50 ) ? $users_list : NULL );

$processForm = set_tab();

if( $processForm ) {
	$featureToggles = array_merge( $formFeaturesBit,$formFeaturesHelp,array( 'site_contact' => 0 ) );
	foreach( array_keys( $featureToggles ) as $item ) {
		simple_set_toggle( $item );
	}
	simple_set_value( "contact_user" );
}

$gBitSystem->setHelpInfo('Features','Settings','Help with the features settings');


?>
