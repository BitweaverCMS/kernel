<?php
/**
 * $Header$
 *
 * Copyright ( c ) 2004 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @package kernel
 * @subpackage modules
 */
extract( $moduleParams );

if( !empty( $module_params ) ) {
	$gBitSmarty->assign( 'modParams', $module_params );
}

global $gStructure, $gContent;
if( !$gStructure and $gContent ) {
	$structs = $gContent->getStructures();
	if ( count($structs)  > 1 ) {
		$gStructure = new LibertyStructure( $structs[0]['structure_id'] );
		if( $gStructure->load() ) {
			$gStructure->loadNavigation();
			$gStructure->loadPath();
			$gBitSmarty->assign( 'structureInfo', $gStructure->mInfo );
		}
	}
}

if( $gStructure and !empty($gStructure->mInfo['structure_path']) ) {
	$secondbox = 0;
	$tree = 1;
	$gStructure->mInfo['structure_path'][0]['structure_id'];			
	if( $gStructure->mInfo['parent']['structure_id'] == 4 ) $sidebox = $gStructure->mInfo['content_id'] - 3;
	elseif( $gStructure->mInfo['parent']['content_id'] > 4 ) $sidebox = $gStructure->mInfo['parent']['content_id'] - 3;
	else $sidebox = 1;
	if( $gStructure->mInfo['content_id'] != 4 ) {
		$menu = $gStructure->buildTreeToc( $tree );
		$gBitSmarty->assignByRef( 'menu', $menu[0]['sub'] );
		$gBitSmarty->assign( 'sidebox', $sidebox );
		if ($secondbox) {
			$secondmenu = $gStructure->buildTreeToc( $secondbox );
			$gBitSmarty->assignByRef( 'secondmenu', $secondmenu[0]['sub'] );
		}
	}	
} else {
	require_once( LIBERTY_PKG_CLASS_PATH.'LibertyStructure.php' );
	$gStructure = new LibertyStructure( 1 );
	$menu = $gStructure->buildTreeToc( 1 );
	$gBitSmarty->assignByRef( 'menu', $menu[0]['sub'] );
}

?>
