<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/modules_inc.php,v 1.10 2006/12/02 17:13:00 spiderr Exp $
 * @package kernel
 * @subpackage functions
 */

// $Header: /cvsroot/bitweaver/_bit_kernel/Attic/modules_inc.php,v 1.10 2006/12/02 17:13:00 spiderr Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

global $gBitSmarty, $gBitSystem, $gBitThemes, $gBitUser;

clearstatcache();
$now = $gBitSystem->getUTCTime();

/* this doesn't seem to used anywhere - xing
if (!$gBitUser->isAdmin() ) {
    $user_groups = $gBitUser->getGroups();
} else {
	$listHash = array( 'sort_mode' => 'group_name_desc' );
    $allgroups = $gBitUser->getAllGroups( $listHash );

    $user_groups = array();

    foreach ($allgroups["data"] as $grp) {
        $user_groups[] = $grp["group_name"];
    }
}
*/

global $module_column, $gHideModules;
if( $gBitSystem->mLayout && empty( $gHideModules ) ) {
	foreach( array_keys( $gBitSystem->mLayout ) as $column ) {
		if ($column != 'c') {	// We don't need to pre-fetch center columns
			$module_column = $column;
			for ($i = 0; $i < count( $gBitSystem->mLayout[$column] ); $i++) {
				$r = &$gBitSystem->mLayout[$column][$i];
				if( !empty( $r['visible'] ) ) {
					list( $package, $template ) = split(  '/', $r['module_rsrc'] );
					if( $package == '_custom:custom' ) {
						global $gBitLanguage;
						//print("Es user module");
						// We're gonna run our own cache mechanism for user_modules
						// the cache is here to avoid calls to consumming queries,
						// each module is different for each language because of the strings
						$cacheDir = TEMP_PKG_PATH.'modules/cache/';
						if( !is_dir( $cacheDir ) ) {
							mkdir_p( $cacheDir );
						}
						$cachefile = $cacheDir.'_custom.'.$gBitLanguage->mLanguage.'.'.$template.'.tpl.cache';
						if( file_exists( $cachefile ) && !( ($now - filemtime($cachefile)) > $r["cache_time"]) ) {
							//print("Usando cache<br/>");
							$fp = fopen($cachefile, "r");
							$data = fread($fp, filesize($cachefile));
							fclose ($fp);
							$r["data"] = $data;
						} else {
							if( $info = $gBitThemes->getCustomModule( $template ) ) {
								// Ahora usar el template de user
								$gBitSmarty->assign_by_ref('user_title', $info["title"]);
								$gBitSmarty->assign_by_ref('user_data', $info["data"]);
								$gBitSmarty->assign_by_ref('user_module_name', $info["name"]);
								$data = $gBitSmarty->fetch( 'bitpackage:themes/custom_module.tpl' );
								$fp = fopen($cachefile, "w+");
								fwrite($fp, $data, strlen($data));
								fclose ($fp);
								$r["data"] = $data;
							}
						}
					} else {
						list( $rsrc_type, $package ) = split(  ':', $package );
						//print("Cache: $cachefile PHP: $phpfile Template: $template<br/>");
						if( !$r['module_rows'] ) {
							$r['module_rows'] = 10;
						}
						global $module_rows, $module_params, $module_title;
						$gBitSmarty->assign_by_ref( 'module_rows', $module_rows = $r['module_rows'] );
						if( strpos( trim( $r['params'] ), ' ' ) ) {
							$module_params = parse_xml_attributes( $r['params'] );
						} else {
							parse_str( $r["params"], $module_params );
						}
						$module_title = ( isset( $r['title'] ) ? tra( $r['title'] ) : ( isset( $module_params['title'] ) ? $module_params['title'] : NULL ) );
						$pattern[0] = "/.*\/mod_(.*)\.tpl/";
						$replace[0] = "$1";
						$pattern[1] = "/_/";
						$replace[1] = " ";
						$gBitSmarty->assign( 'moduleTitle', ( isset( $module_title ) ? tra( $module_title ) : tra( ucfirst( preg_replace( $pattern, $replace, $r['module_rsrc'] ) ) ) ) );
						$gBitSmarty->assign_by_ref( 'module_rows', $r['module_rows'] );
						$gBitSmarty->assign_by_ref( 'module_id', $r["module_id"] );
						$gBitSmarty->assign_by_ref( 'module_layout', $r["layout"] );
						$colkey = ($column == 'r') ? 'right' : 'left';
						$gBitSmarty->assign_by_ref( 'colkey', $colkey);
						$r['data'] = $gBitSmarty->fetch( $r['module_rsrc'] );
						unset( $module_rows );
					}
				}
				unset( $data );
			}
			$gBitSmarty->assign_by_ref( $column.'_modules', $gBitSystem->mLayout[$column] );
		}
	}
}
?>
