<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/phpinfo.php,v 1.2 2006/04/11 13:05:16 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );
phpinfo();
?>
