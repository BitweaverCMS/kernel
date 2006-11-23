<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_features_inc.php,v 1.22 2006/11/23 15:18:18 squareing Exp $

$formBit = array(
	'pretty_urls' => array(
		'label' => 'Use Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. Using this feature requires Apache <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a> support in the web server (usually installed by default), Depending on the Web server configuration, it may be necessary to modify the default .htaccess files when using this feature.',
		'page' => 'PrettyUrls',
	),
	'pretty_urls_extended' => array(
		'label' => 'Use Extended Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. Using this feature requires Apache <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a> support in the web server (usually installed by default), This extended version adds a /view tag to the URLs to make them unambigious for rewrites.  It will be necessary to modify the default .htaccess files when using this feature.',
		'page' => 'FeaturePrettyUrlsExtended',
	),
);
$gBitSmarty->assign( 'formBit',$formBit );

$formHelp = array(
	'site_form_help' => array(
		'label' => 'Help Notes',
		'note' => 'Show inline help notes in forms such as the one you are reading now.',
	),
	'site_online_help' => array(
		'label' => 'Online Help Links',
		'note' => 'Display links to relevant online help pages found on www.bitweaver.org.',
	),
	'site_edit_help' => array(
		'label' => 'Show Wiki Help',
		'note' => 'Displays <strong>extensive</strong> (about 50kb) help regarding tikiwiki syntax and wiki plugins whenever there is an entry form that takes wiki syntax.',
	),
	'site_help_popup' => array(
		'label' => 'Use Popup for Help Items',
		'note' => 'This will place a clickable icon after form elements for the extended help information.',
	),
);
$gBitSmarty->assign( 'formHelp',$formHelp );

$formMisc = array(
	'site_direct_pagination' => array(
		'label' => 'Use direct pagination links',
		'note' => 'Use direct pagination links instead of the small pagination box. Links are cleverly generated depending on the number of pages available.',
	),
	'site_output_obzip' => array(
		'label' => 'Use gzipped output',
		'note' => 'Send gzip compressed data to browsers that support it. This will reduce download times for users.',
	),
	// want to remove this setting. we use addHit() which is clever
	'users_count_admin_pageviews' => array(
		'label' => 'Count admin pageviews',
		'note' => '',
	),
);
$gBitSmarty->assign( 'formMisc',$formMisc );

if( !empty( $_REQUEST['change_prefs'] ) ) {
	$featureToggles = array_merge( $formBit, $formHelp, $formMisc );
	foreach( $featureToggles as $item => $info ) {
		simple_set_toggle( $item, KERNEL_PKG_NAME );
	}

	$simpleValues = array(
		"max_records",
		"site_url_index",
	);

	foreach( $simpleValues as $svitem ) {
		simple_set_value( $svitem, KERNEL_PKG_NAME );
	}

	simple_set_value( 'site_display_reltime', KERNEL_PKG_NAME );

	// Special handling for tied fields: bit_index and site_url_index
	if (!empty($_REQUEST["site_url_index"]) && $_REQUEST["bit_index"] == 'users_custom_home') {
		$_REQUEST["bit_index"] = $_REQUEST["site_url_index"];
	}

	$refValue = array(
		"site_long_date_format",
		"site_long_time_format",
		"site_short_date_format",
		"site_short_time_format",
		"bit_index"
	);

	foreach( $refValue as $britem ) {
		byref_set_value( $britem, "", KERNEL_PKG_NAME );
	}
}

$gBitSystem->setHelpInfo('Features','Settings','Help with the features settings');
?>
