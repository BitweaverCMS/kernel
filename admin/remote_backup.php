<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/remote_backup.php,v 1.3 2009/10/01 14:17:01 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Call with eg.
// http://localhost/tiki/kernel/remote_backup.php?generate=1&my_word=ThisIsMySecretBackupWord"

// PLEASE UNCOMMENT THIS LINE TO ACTIVATE REMOTE BACKUPS (DISABLED IN THE DISTRIBUTION)
die;

require_once( '../../bit_setup_inc.php' );
include_once('lib/backups/backupslib.php');
if(isset($_REQUEST["generate"])) {
    if(isset($_REQUEST["my_word"]) &&
       $_REQUEST["my_word"] == "YOUR PASSWORD FOR BACKUPS HERE" ) {
        $filename = md5($gBitSystem->genPass()).'.sql';
        $backuplib->backup_database("backups/$bitdomain$filename");
        echo "Done";
    }
}

die;

?>
