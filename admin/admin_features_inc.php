<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_features_inc.php,v 1.14 2006/04/14 19:36:18 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$formFeaturesBit = array(
	'pretty_urls' => array(
		'label' => 'Use Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. Using this feature requires Apache <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a> support in the web server (usually installed by default), Depending on the Web server configuration, it may be necessary to modify the default .htaccess files when using this feature.',
		'page' => 'PrettyUrls',
		'pkg' => KERNEL_PKG_NAME,
	),
	'pretty_urls_extended' => array(
		'label' => 'Use Extended Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. Using this feature requires Apache <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a> support in the web server (usually installed by default), This extended version adds a /view tag to the URLs to make them unambigious for rewrites.  It will be necessary to modify the default .htaccess files when using this feature.',
		'page' => 'FeaturePrettyUrlsExtended',
		'pkg' => KERNEL_PKG_NAME,
	),
	'feature_jscalendar' => array(
		'label' => 'Enable JSCalendar',
		'note' => 'JSCalendar is a javascript calendar popup that allows you to easily select a date using an easy to use and appealing interface.',
		'page' => 'JSCalendar',
		'pkg' => THEMES_PKG_NAME,
	),
	'themes_edit_css' => array(
		'label' => 'Edit Css',
		'note' => 'Enables you to edit CSS files from within your browser to customise your site style according to your desires.',
		'pkg' => THEMES_PKG_NAME,
	),
	'categories_objects' => array(
		'label' => 'Show Category Objects',
		'note' => 'Display a list of items that are part of a particular category at the bottom of the page.',
		'pkg' => CATEGORIES_PKG_NAME,
	),
	'categories_path' => array(
		'label' => 'Show Category Path',
		'note' => 'Display the category path at the top of the page',
		'pkg' => CATEGORIES_PKG_NAME,
	),
);
if( $gBitSystem->isPackageActive( 'stats' ) ) {
	$formFeaturesBit['referer_stats'] = array(
		'label' => 'Referer Statistics',
		'note' => 'Records statistics including HTTP_REFERRER',
		'pkg' => STATS_PKG_NAME,
	);
}
$gBitSmarty->assign( 'formFeaturesBit',$formFeaturesBit );

$formFeaturesHelp = array(
	'help_notes' => array(
		'label' => 'Help Notes',
		'note' => 'Show inline help notes in forms such as the one you are reading now.',
		'pkg' => KERNEL_PKG_NAME,
	),
	'help' => array(
		'label' => 'Online Help Links',
		'note' => 'Display links to relevant online help pages found on www.bitweaver.org.',
		'pkg' => KERNEL_PKG_NAME,
	),
	'wiki_help' => array(
		'label' => 'Show Wiki Help',
		'note' => 'Displays <strong>extensive</strong> (about 50kb) help regarding tikiwiki syntax and wiki plugins whenever there is an entry form that takes wiki syntax.',
		'pkg' => KERNEL_PKG_NAME,
	),
	'help_popup' => array(
		'label' => 'Use Popup for Help Items',
		'note' => 'This will place a clickable icon after form elements for the extended help information.',
		'pkg' => KERNEL_PKG_NAME,
	),
);
$gBitSmarty->assign( 'formFeaturesHelp',$formFeaturesHelp );

$processForm = set_tab();

if( $processForm ) {
	$featureToggles = array_merge( $formFeaturesBit,$formFeaturesHelp,array( 'site_contact' => 0 ) );
	foreach( $featureToggles as $item => $info ) {
		simple_set_toggle( $item, $info['pkg'] );
	}
}

$gBitSystem->setHelpInfo('Features','Settings','Help with the features settings');


?>
