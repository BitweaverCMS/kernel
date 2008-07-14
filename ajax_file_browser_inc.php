<?php
/**
 * Quick guide to this file:
 * $_REQUEST['ajax_path_conf'] is the kernel configuration name that contains the absolute path to the directory where the files are.
 *
 * Safety first:
 * This method was chosen to provide a measure of security since we never pass in an absolute path via the URL this way.
 * Another safety measure is provided that the configuration value set in $gBitSystem->mConfig['$_REQUEST['ajax_path_conf']] is used as 'jail'.
 * Paths outside this 'jail' will be ignored including ../../ or symbolic links.
 * Evil extensions as defined in EVIL_EXTENSION_PATTERN will be ignored as are [dot] files e.g.: .private.txt
 *
 * e.g.:
 * /home/ftp/public/ is the 'jail'
 * /home/ftp/public/ftp -> /home/ftp/ is a symbolic link that points outside the 'jail' and will therefore be ignored completely.
 * Also makes it impossible to import stuff like /home/ftp/public/../../../../../etc/passwd
 *
 * You can define ajax_path_conf in two places with different effects:
 * 1. define the ajax_path_conf when you include the template:
 *    {include file="bitpackage:kernel/ajax_file_browser.tpl" ajax_path_conf=treasury_file_import_path}
 *    This will show a link to "Load Files" which will then load the file list when you click on the link.
 * 2. If you provide $_REQUEST['ajax_path_conf'] when you include it from your php file, all files in the root directory will already be loaded.
 *    $_REQUEST['ajax_path_conf'] = 'treasury_file_import_path';
 *    require_once( KERNEL_PKG_PATH.'ajax_file_browser.php' );
 *
 * NOTE: when you process the imported files, make sure you use realpath() to check of files are really in your 'jail'.
 */
require_once( '../bit_setup_inc.php' );

if( !empty( $_REQUEST['ajax_path_conf'] )) {
	$fileList = ajax_dir_list( $gBitSystem->getConfig( $_REQUEST['ajax_path_conf'] ), ( !empty( $_REQUEST['relpath'] ) ? $_REQUEST['relpath']."/" : NULL ));
	$gBitSmarty->assign( 'fileList', $fileList );
}
$gBitThemes->loadAjax( 'mochikit', array( 'DOM.js', 'Async.js' ));
$gBitThemes->loadJavascript( KERNEL_PKG_PATH."scripts/BitFileBrowser.js", TRUE );

if( $gBitThemes->isAjaxRequest() ) {
	$gBitSmarty->display( 'bitpackage:kernel/ajax_file_browser_inc.tpl' );
}

/**
 * ajax_dir_list 
 * 
 * @param array $pDir Base directory
 * @param array $pRelPath relative path on top of base directory
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function ajax_dir_list( $pDir, $pRelPath = NULL ) {
	global $gBitSystem;
	$ret = $files = array();

	if( !empty( $pDir ) && is_dir( $pDir.$pRelPath )) {
		if( $handle = opendir( $pDir.$pRelPath )) {
			while( FALSE !== ( $file = readdir( $handle ))) {
				if( !preg_match( "#^\.#",$file ) && is_readable( $pDir.$pRelPath.$file )) {
					array_push( $files, $file );
				}
			}
			sort( $files );
			foreach( $files as $i ) {
				$relFile = $pRelPath.$i;
				$file = realpath( $pDir.$relFile );
				if( strpos( $file, $pDir ) === 0 ) {
					$info = array(
						'name'    => $i,
						'relpath' => $relFile,
						'indent'  => ( count( explode( '/', $relFile )) * 10 ),
						'size'    => filesize( $file ),
						'mtime'   => filemtime( $file ),
					);
					if( is_dir( $file )) {
						$ret['dir'][$i] = $info;
					} elseif( !preg_match( EVIL_EXTENSION_PATTERN, $file )) {
						$ret['file'][$i] = $info;
					}
				}
			}
			closedir( $handle );
		}
	}

	if( empty( $ret )) {
		$ret['file'][] = array(
			'indent'  => ( count( explode( '/', $pRelFile )) * 10 ),
		);
	}

	return $ret;
}
?>
