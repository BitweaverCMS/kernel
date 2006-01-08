<?php
// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_system.php,v 1.1.1.1.2.3 2006/01/08 01:36:56 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once( '../../bit_setup_inc.php' );

function du($path) {
	if (!$path or !is_dir($path)) return 0;
	$total = 0;
	$cant = 0;
	$back = array();
	$all = opendir($path);
	while ($file = readdir($all)) {
		if (is_dir($path.'/'.$file) and $file <> ".." and $file <> "." and $file <> "CVS") {
			$du = du($path.'/'.$file);
			$total+= $du['total'];
			$cant+= $du['cant'];
			unset($file);
		} elseif (!is_dir($path.'/'.$file)) {
			$stats = stat($path.'/'.$file);
			$total += $stats['size'];
			$cant++;
			unset($file);
		}
	}
	closedir($all);
	unset($all);
	$back['total'] = $total;
	$back['cant'] = $cant;
	return $back;
}

function cache_templates($path, $oldlang, $newlang) {
	global $gBitLanguage;
	global $gBitSmarty;
	if (!$path or !is_dir($path)) return 0;
	if ($dir = opendir($path)) {
		while (false !== ($file = readdir($dir))) {
			$a=explode(".",$file);
			$ext=strtolower(end($a));
			if (substr($file,0,1) == "." or $file == 'CVS') continue;
			if (is_dir($path."/".$file)) {
				cache_templates($path."/".$file, $oldlang, $newlang);
			} else {
				if ($ext=="tpl") {
					$file=$path."/".$file;
					$gBitSmarty->_compile_id = $newlang;
					$comppath=$gBitSmarty->_get_compile_path($file);
					//rewrite the language thing, see setup_smarty.php
					$comppath=preg_replace("#/".$oldlang."/#","/".$newlang."/",$comppath,1);
					if(!$gBitSmarty->_is_compiled($file,$comppath)) {
						$gBitSmarty->_compile_resource($file,$comppath);
					}
				}
			}
		}
		closedir($dir);
	}
}

if (!$gBitUser->isAdmin()) {
	$gBitSmarty->assign('msg', tra("You dont have permission to use this feature"));
	$gBitSystem->display( 'error.tpl' );
	die;
}

$done = '';
$output = '';
$buf = '';

if (isset($_GET['do'])) {
	if ($_GET['do'] == 'templates_c' || $_GET['do'] == 'all') {
		unlink_r(TEMP_PKG_PATH."templates_c/$bitdomain");
	}
	if ($_GET['do'] == 'lang_cache' || $_GET['do'] == 'all') {
		unlink_r(TEMP_PKG_PATH."lang");
	}
	if ($_GET['do'] == 'modules_cache' || $_GET['do'] == 'all') {
		unlink_r(TEMP_PKG_PATH."modules/cache/$bitdomain");
	}
	if ($_GET['do'] == 'cache' || $_GET['do'] == 'all') {
		unlink_r(TEMP_PKG_PATH."cache/$bitdomain");
	}
}

if (isset($_GET['compiletemplates'])) {
	cache_templates(BIT_TEMP_PATH.'templates_c', $gBitLanguage->mLanguage, $_GET['compiletemplates']);
}

$languages = array();
$languages = $gBitLanguage->listLanguages();
ksort($languages);

$du['templates_c'] = du(TEMP_PKG_PATH.'templates_c');
$du['lang'] = du(TEMP_PKG_PATH.'lang');
$du['modules'] = du(TEMP_PKG_PATH.'modules/cache');
$du['cache'] = du(TEMP_PKG_PATH.'cache');
$gBitSmarty->assign('du', $du);

$templates=array();
$langdir = TEMP_PKG_PATH."templates_c/".$gBitSystem->getPreference('style')."/";
$gBitSmarty->assign('langdir', $langdir);
foreach(array_keys($languages) as $clang) {
	if(is_dir($langdir.$clang)) {
		$templates[$clang] = du($langdir.$clang);
	} else {
		$templates[$clang] = array("cant"=>0,"total"=>0);
	}
}
$gBitSmarty->assign_by_ref('templates', $templates);

$gBitSystem->display( 'bitpackage:kernel/admin_system.tpl');
?>
