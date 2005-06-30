<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_features_inc.php,v 1.1.1.1.2.1 2005/06/30 03:29:34 jht001 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//This doen't scale very well when you have 1000's of users
//$users_list = $gBitUser->get_users_names();
//$smarty->assign( 'users_list',$users_list );

$formFeaturesTiki = array(
	'pretty_urls' => array(
		'label' => 'Use Pretty URLs',
		'note' => 'In addition to making the URL easier to read and remember it enhances search engine results. *requires <a href="http://httpd.apache.org/docs-2.0/mod/mod_rewrite.html">mod_rewrite</a>, which comes installed by default on with apache',
		'page' => 'PrettyUrls',
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
$smarty->assign( 'formFeaturesTiki',$formFeaturesTiki );

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
$smarty->assign( 'formFeaturesHelp',$formFeaturesHelp );

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
	/* in preparation of dill release
	'feature_jstabs' => array(
		'label' => 'Javascript Tabs',
		'note' => 'If you should not like the Javascript Tabs that are used throughout bitweaver, or if you experience problems with them, you can desable them here.<br />We recommend that you add something like <strong>.tabsystem h4 {display: none;}</strong> to your css file to remove duplicate headings. Some pages might be disorienting if this feature is disabled.',
	),
	*/
);
$smarty->assign( 'formFeaturesContent',$formFeaturesContent );

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

if( $gBitSystem->isFeatureActive( 'debug' ) ) {
	$formFeaturesAdmin['feature_debug_console'] = array(
			'label' => 'Debug Console',
			'note' => 'Helps you Debug bitweaver',
			'page' => 'DebugConsole',
		);
}

$smarty->assign( 'formFeaturesAdmin',$formFeaturesAdmin );

$processForm = set_tab();

if( $processForm ) {
	
	$featureToggles = array_merge( $formFeaturesTiki,$formFeaturesContent,$formFeaturesAdmin,$formFeaturesHelp );
	foreach( $featureToggles as $item => $data ) {
		simple_set_toggle( $item );
	}
	simple_set_value( "contact_user" );
}

$gBitSystem->setHelpInfo('Features','Settings','Help with the features settings');


?>
