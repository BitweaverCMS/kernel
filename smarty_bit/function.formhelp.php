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
 *									only displayed if site_form_help is enabled
 *			- link		(optional)	provide a link to an internal page (avoids the problem with links being inerpreted
 *									prematurely by the tra() function
 *									<package>/<path to file>/<title>
 *			- package	(optional)	creates a page to 'Package'.ucfirst( $package ) and takes precedence over $page, should both be set.
 *									only dispalyed if help is enabled
 *			- install	(optional)	used for packages that require a separate installation
 *									passed in as an array:
 *										package => name of package to be installed
 *										file => path to installation file e.g.: admin/install.php
 *			- page		(optional)	page name on bitweaver
 *									only dispalyed if help is enabled
 *			- force		(optional)	if set, it will always dipslay this entry regardless of the feature settings
 */
function smarty_function_formhelp( $pParams, &$gBitSmarty ) {
	$atts = $ret_note = $ret_page = $ret_link = $ret_install = '';

	if( !empty( $pParams['hash'] ) ) {
		$hash = &$pParams['hash'];
	} else {
		// maybe params were passed in separately
		$hash = &$pParams;
	}

	// we need to do some hash modification if we're in the installer
	if( !empty( $hash['is_installer'] )) {
		if( !empty( $hash['note']['upgrade'] )) {
			$hash['note']['version'] = $hash['note']['upgrade'];
			unset( $hash['note']['upgrade'] );
		}
	}

	foreach( $hash as $key => $val ) {
		switch( $key ) {
			case 'note':
			case 'warning':
			case 'link':
			case 'page':
				$rawHash[$key] = $val;
				break;
			case 'label':
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
		$rawHash['page'] = ucfirst( $package ).'Package';
	}

	// if link was passed in as a string, convert it into an array
	if( !empty( $rawHash['link'] ) && is_string( $rawHash['link'] ) ) {
		$l = explode( '/', $rawHash['link'] );
		unset( $rawHash['link'] );
		// package is first, title last, and all remaining elements file (can be 'foo/bar.php' as well)
		$rawHash['link']['package'] = array_shift( $l );
		$rawHash['link']['title']   = array_pop( $l );
		$rawHash['link']['file']    = implode( '/', $l );
	}

	global $gBitSystem;
	if( $gBitSystem->isFeatureActive( 'site_online_help' ) || $gBitSystem->isFeatureActive( 'site_form_help' ) || $force == 'y' ) {
		if( !empty( $rawHash ) ) {
			if( !empty( $rawHash['page'] ) && ( $gBitSystem->isFeatureActive('site_online_help') || $force == 'y' ) ) {
				$ret_page = '<strong>'.tra( 'Online help' ).'</strong>: <a class=\'external\' href=\'http://doc.bitweaver.org/wiki/index.php?page='.$rawHash['page'].'\'>'.$rawHash['page'].'</a><br />';
			}

			if( !empty( $rawHash['link'] ) && ( $gBitSystem->isFeatureActive('site_online_help') || $force == 'y' ) ) {
				if( is_array( $rawHash['link'] ) ) {
					$ret_link  = '<strong>'.tra( 'IntraLink' ).'</strong>: ';
					$ret_link .= '<a href=\'';
					$ret_link .= constant( strtoupper( $rawHash['link']['package'] ).'_PKG_URL' ).$rawHash['link']['file'];
					$ret_link .= '\'>'.tra( $rawHash['link']['title'] ).'</a>';
				}
			}

			if( ( !empty( $rawHash['note'] ) && $gBitSystem->isFeatureActive('site_form_help') ) || ( !empty( $force ) && !empty( $rawHash['note'] ) ) ) {
				if( is_array( $rawHash['note'] ) ) {
					foreach( $rawHash['note'] as $name => $value ) {
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
					$ret_note .= tra( $rawHash['note'] ).'<br />';
				}
			}

			if( !empty( $rawHash['warning'] ) ) {
				$ret_note .= '<span class="warning">'.tra( $rawHash['warning'] ).'</span><br />';
			}

			// join all the output content into one string
			$content = $ret_note.$ret_page.$ret_link.$ret_install;

			$html = '';
			// using the overlib popup system
			if( !empty( $content ) ) {
				if( $gBitSystem->isFeatureActive('site_help_popup') ) {
					require_once $gBitSmarty->_get_plugin_filepath('function','popup');
					require_once $gBitSmarty->_get_plugin_filepath('function','biticon');

					$gBitSmarty->assign( 'title',tra('Extended Help') );

					$gBitSmarty->assign( 'content', $content );
					$gBitSmarty->assign( 'closebutton', TRUE );
					$text = $gBitSmarty->fetch('bitpackage:kernel/popup_box.tpl');
					$text = preg_replace( '/"/',"'",$text );

					$popup = array(
						'trigger' => 'onclick',
						'text' => $text,
						'fullhtml' => '1',
						'sticky' => '1',
						'timeout' => '8000',
					);

					$biticon = array(
						'ipackage' => 'icons',
						'iname' => 'dialog-information',
						'iforce' => 'icon',
						'iexplain' => 'Extended Help',
					);

					$html .= ' <span class="formhelppopup" '.$atts.'>&nbsp;';
					$html .= '<a '.smarty_function_popup( $popup, $gBitSmarty ).'>';
					$html .= smarty_function_biticon( $biticon, $gBitSmarty );
					$html .= '</a>';
					$html .= '</span>';
				} else {
					$html .= '<div class="formhelp" '.$atts.'>'.$content.'</div>';
				}
			}

			return $html;
		}
	}
}
?>
