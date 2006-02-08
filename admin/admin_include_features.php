<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_include_features.php,v 1.4 2006/02/08 21:51:14 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$features_toggles = array(
	"autolinks"               => 'kernel',
	"babelfish"               => 'kernel',
	"babelfish_logo"          => 'languages',
	"feature_banners"         => 'tidbits',
	"banning"                 => 'tidbits',
	"bot_bar"                 => 'themes',
	"feature_categoryobjects" => 'categories',
	"feature_categorypath"    => 'categories',
	"feature_comm"            => 'xmlrpc',
	"site_contact"            => 'kernel', // ???
	"contact_anon"            => 'kernel', // ???
	"custom_home"             => 'kernel',
	"feature_debug_console"   => 'kernel',
	"feature_editcss"         => 'themes',
	"feature_featuredLinks"   => 'kernel',
	"feature_html_pages"      => 'kernel',
	"feature_jscalendar"      => 'kernel',
	"left_column"             => 'themes',
	"feature_modulecontrols"  => 'themes',
	"referer_stats"           => 'stats',
	"right_column"            => 'themes',
	"feature_stats"           => 'stats',
	"top_bar"                 => 'themes',
	"feature_view_tpl"        => 'kernel',
	"layout_section"          => 'themes',
	"user_assigned_modules"   => 'tidbits' //???
);

// Process Features form(s)
if (isset($_REQUEST["features"])) {
	foreach ($features_toggles as $toggle => $package) {
		simple_set_toggle ($toggle, $package);
	}
}

?>
