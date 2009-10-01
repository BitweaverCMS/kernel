<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/phpinfo.php,v 1.3 2009/10/01 13:45:43 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );
phpinfo();
?>
