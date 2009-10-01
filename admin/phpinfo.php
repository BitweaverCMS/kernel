<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/phpinfo.php,v 1.4 2009/10/01 14:17:01 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );
phpinfo();
?>
