<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_include_features.php,v 1.1 2005/06/19 04:52:54 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$features_toggles = array(
	"feature_autolinks",
	"feature_babelfish",
	"feature_babelfish_logo",
	"feature_banners",
	"feature_banning",
	"feature_bot_bar",
	"feature_categoryobjects",
	"feature_categorypath",
	"feature_comm",
	"feature_contact",
	"contact_anon",
	"feature_custom_home",
	"feature_debug_console",
	"feature_edit_templates",
	"feature_editcss",
	"feature_featuredLinks",
	"feature_html_pages",
	"feature_jscalendar",
	"feature_left_column",
	"feature_modulecontrols",
	"feature_referer_stats",
	"feature_right_column",
	"feature_stats",
	"feature_top_bar",
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
