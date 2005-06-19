<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/phpinfo.php,v 1.1 2005/06/19 04:52:54 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
$gBitSystem->verifyPermission( 'bit_p_admin' );
phpinfo();
?>
