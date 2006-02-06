<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_include_features.php,v 1.3 2006/02/06 22:56:46 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$features_toggles = array(
	"autolinks",
	"babelfish",
	"babelfish_logo",
	"feature_banners",
	"banning",
	"bot_bar",
	"feature_categoryobjects",
	"feature_categorypath",
	"feature_comm",
	"site_contact",
	"contact_anon",
	"custom_home",
	"feature_debug_console",
	"feature_editcss",
	"feature_featuredLinks",
	"feature_html_pages",
	"feature_jscalendar",
	"left_column",
	"feature_modulecontrols",
	"referer_stats",
	"right_column",
	"feature_stats",
	"top_bar",
	"feature_view_tpl",
	"layout_section",
	"user_assigned_modules"
);

// Process Features form(s)
if (isset($_REQUEST["features"])) {
	
	foreach ($features_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
}

?>
