<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/error.php,v 1.3 2005/08/01 18:40:33 squareing Exp $
 * @package kernel
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );



// Display the template
$gBitSmarty->assign('msg', strip_tags($_REQUEST["error"]));
$gBitSystem->display( 'error.tpl' );

?>
