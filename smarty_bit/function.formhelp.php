<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {formhelp} function plugin
 *
 * Type:	function
 * Name:	formhelp
 * Input:
 *			- note		(optional)	words that are displayed, can also be an array, where: 'key: value'<br /> is printed
 *									only displayed if feature_helpnotes is enabled
 *			- link		(optional)	provide a link to an internal page (avoids the problem with links being inerpreted
 *									prematurely by the tra() function
 *									<package>/<path to file>/<title>
 *			- package	(optional)	creates a page to 'Package'.ucfirst( $package ) and takes precedence over $page, should both be set.
 *									only dispalyed if feature_help is enabled
 *			- install	(optional)	used for packages that require a separate installation
 *									passed in as an array:
 *										package => name of package to be installed
 *										file => path to installation file e.g.: admin/install.php
 *			- page		(optional)	page name on bitweaver
 *									only dispalyed if feature_help is enabled
 *			- force		(optional)	if set, it will always dipslay this entry regardless of the feature settings
 */
function smarty_function_formhelp( $params, &$gBitSmarty ) {
	if( !empty( $params['hash'] ) ) {
		$hash = &$params['hash'];
	} else {
		// maybe params were passed in separately
		$hash = &$params;
	}

	foreach( $hash as $key => $val ) {
		switch( $key ) {
			case 'note':
			case 'link':
			case 'label':
			case 'page':
			case 'package':
			case 'install':
			case 'force':
				$$key = $val;
				break;
			default:
				if( $val ) {
					$atts .= $key.'="'.$val.'" ';
				}
				break;
		}			
	}

	if( !empty( $package ) ) {
		$page = ucfirst( $package ).'Package';
	}

	// if link was passed in as a string, convert it into an array
	if( !empty( $link ) && is_string( $link ) ) {
		$l = explode( '/', $link );
		unset( $link );
		// package is first, title last, and all remaining elements file (can be 'foo/bar.php' as well)
		$link['package'] = array_shift( $l );
		$link['title']   = array_pop( $l );
		$link['file']    = implode( '/', $l );
	}

	global $gBitSystem;
	if( $gBitSystem->getPreference( 'feature_help' ) == 'y' || $gBitSystem->getPreference( 'feature_helpnotes' ) == 'y' || $force == 'y' ) {
		if( !empty( $note ) || !empty( $page ) || !empty( $link ) ) {
			if( !empty( $page ) && ( $gBitSystem->getPreference('feature_help') == 'y' || $force == 'y' ) ) {
				$ret_page = '<strong>'.tra( 'Online help' ).'</strong>: <a class=\'external\' href=\'http://doc.bitweaver.org/wiki/index.php?page='.$page.'\'>'.$page.'</a><br />';
			}

			if( !empty( $link ) && ( $gBitSystem->getPreference('feature_help') == 'y' || $force == 'y' ) ) {
				if( is_array( $link ) ) {
					$ret_link  = '<br /><strong>'.tra( 'IntraLink' ).'</strong>: ';
					$ret_link .= '<a href=\'';
					$ret_link .= constant( strtoupper( $link['package'] ).'_PKG_URL' ).$link['file'];
					$ret_link .= '\'>'.tra( $link['title'] ).'</a>';
				}
			}

			$ret_note = '';
			if( ( !empty( $note ) && $gBitSystem->getPreference('feature_helpnotes') == 'y' ) || ( !empty( $force ) && !empty( $note ) ) ) {
				if( is_array( $note ) ) {
					foreach( $note as $name => $value ) {
						if( $name == 'install' ) {
							$ret_install  = '<strong>'.tra( 'Install' ).'</strong>: '.tra( 'To use this package, you will first have to run the package specific installer' ).': ';
							$ret_install .= '<a href=\'';
							$ret_install .= constant( strtoupper( $value['package'] ).'_PKG_URL' ).$value['file'];
							$ret_install .= '\'>'.ucfirst( $value['package'] ).'</a>';
						} else {
							$ret_note .= '<strong>'.ucfirst( tra( $name ) ).'</strong>: '.tra( $value ).'<br />';
						}
					}
				} else {
					$ret_note .= tra( $note );
				}
			}

			// join all the output content into one string
			$content  = $ret_page;
			$content .= $ret_note;
			$content .= $ret_link;
			$content .= $ret_install;

			// using the overlib popup system
			if( $gBitSystem->getPreference('feature_helppopup') == 'y') {
				require_once $gBitSmarty->_get_plugin_filepath('function','popup');
				require_once $gBitSmarty->_get_plugin_filepath('function','biticon');

				$gBitSmarty->assign( 'title',tra('Extended Help') );

				$gBitSmarty->assign( 'content',$content );
				$text = $gBitSmarty->fetch('bitpackage:kernel/popup_box.tpl');
				$text = ereg_replace( '"',"'",$text );

				$popup = array(
					'trigger' => 'onclick',
					'text' => $text,
					'fullhtml' => '1',
					'sticky' => '1',
					'timeout' => '8000',
				);

				$biticon = array(
					'ipackage' => 'liberty',
					'iname' => 'info',
					'iforce' => 'icon',
					'iexplain' => 'Extended Help',
				);

				$html = ' <span class="formhelppopup" '.$atts.'>&nbsp;';
				$html .= '<a '.smarty_function_popup( $popup, $gBitSmarty ).'>';
				$html .= smarty_function_biticon( $biticon, $gBitSmarty );
				$html .= '</a>';
				$html .= '</span>';
			} else {
				$html = '<div class="formhelp" '.$atts.'>';
				$html .= $content;
				$html .= '</div>';
			}

			return $html;
		}
	}
}
?>
