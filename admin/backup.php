<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/backup.php,v 1.3 2005/08/01 18:40:35 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
require_once ( KERNEL_PKG_PATH.'backups_lib.php' );

// Check for admin permission
$gBitSystem->verifyPermission( 'bit_p_admin' );

global $gBitDbType;

$backupPath = STORAGE_PKG_PATH."backups/$bitdomain";
mkdir_p( $backupPath );

if (isset($_REQUEST["generate"])) {
	
	$filename = md5($gBitSystem->genPass()). '.sql';

	$backuplib->backup_database( $backupPath.$filename );
}

$gBitSmarty->assign('restore', 'n');

if (isset($_REQUEST["restore"])) {
	
	$gBitSmarty->assign('restore', 'y');

	$gBitSmarty->assign('restorefile', basename($_REQUEST["restore"]));
}

if (isset($_REQUEST["rrestore"])) {
	
	$backuplib->restore_database( $backupPath.basename($_REQUEST["rrestore"]));
}

if (isset($_REQUEST["remove"])) {
	
	$filename = $backupPath.basename($_REQUEST["remove"]);

	unlink ($filename);
}

if (isset($_REQUEST["upload"])) {
	
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "r");

		$fw = fopen( $backupPath.$_FILES['userfile1']['name'], "w");

		while (!feof($fp)) {
			$data = fread($fp, 4096);

			fwrite($fw, $data);
		}

		fclose ($fp);
		fclose ($fw);
		unlink ($_FILES['userfile1']['tmp_name']);
	} else {
		$gBitSmarty->assign('msg', tra("Upload failed"));

		$gBitSystem->display( 'error.tpl' );
		die;
	}
}

// Get all the files listed in the backups directory
// And put them in an array with the filemtime of
// each file activated
$backups = array();
$h = opendir( $backupPath.$bitdomain );

while ($file = readdir($h)) {
	if (strstr($file, "sql")) {
		$row["filename"] = $file;

		$row["created"] = filemtime( $backupPath.$file );
		$row["size"] = filesize( $backupPath.$file ) / 1000000;
		$backups[] = $row;
	}
}

closedir ($h);
$gBitSmarty->assign_by_ref('backups', $backups);
$gBitSmarty->assign('bitdomain', $bitdomain);


$gBitSystem->display( 'bitpackage:kernel/backup.tpl', tra( 'Backups') );

?>
