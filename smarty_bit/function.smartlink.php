<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @author xing <xing$synapse.plus.com>
 * @link http://www.bitweaver.org/wiki/function_smartlink function.smartlink
 */

/**
 * Smarty {smartlink} function plugin
 *
 * Type:	function<br>
 * Name:	smartlink<br>
 * Input:<br>
 *			- ititle	(required)	words that are displayed<br>
 *			- iatitle	(optional)	alternative text for the link title (rollover, etc.) <br>
 *			- ianchor	(optional)	set the anchor where the link should point to<br>
 *			- isort		(optional)	name of the sort column without the orientation (e.g.: title)<br>
 *			- isort_mode(optional)	this can be used to manually pass the sort mode to smartlink<br>
 *									overrides the value given in $_REQUEST['sort_mode'], which is the default<br>
 *			- iorder	(optional)	if set to asc or desc, it sets the default sorting order of this particular column<br>
 *									asc is default<br>
 *			- idefault	(optional)	if set, it will highlight this link if no $isort_mode is given<br>
 *									this should only be set once per sorting group since it represents the default sorting column<br>
 *			- itra		(optional)	if present then don't translate<br>
 *			- itype		(optional)	can be set to<br>
 *									url		-->		outputs only url<br>
 *									li		-->		outputs link as &lt;li&gt;&lt;a ... &gt;&lt;/li&gt;<br>
 *			- ionclick	(optional)	pass in any actions that should occur onclick<br>
 *			- ibiticon	(optional)	if you want to display an icon instead of text use ibiticon<br>
 *									format is:	'&lt;ipackage&gt;/&lt;iname&gt;'<br>
 *									e.g.:		'liberty/edit'<br>
 *			- iforce	(optional)	pass iforce parameter through to biticon
 *			- iurl		(optional)	pass in a full url
 *			- ifile		(optional)	set the file where the link should point (default is the current file)<br>
 *			- ipackage	(optional)	set the package the link should point to (default is the current package)<br>
 *			- icontrol	(optional)	the hash sent out by postGetList()
 *			- *			(optional)	anything else that gets added to the pile of items is appended using &amp;$key=$val<br>
 *			- ihash		(optional)	you can pass in all the above as an array called ihash or secondary * items common to all links<br>
 * Output:	any kind of link. especially useful when it comes to links used to sort a table, due to the simplified syntax and loss of cumbersome if clauses
 *			also useful if the you want to display an icon as link since smartlink takes biticon parameters<br>
 * Example	- {smartlink ititle="Page Name" isort="title"}<br>
 *			- {smartlink ititle="Page Name" isort="title" iorder="desc" idefault=1}<br>
 *				setting iorder and idefault here, makes this link sort in a descending order by default (iorder)<br>
 *				and it is highlighted when $isort_mode ( or $_REQUEST['sort_mode'] ) is not set (idefault)<br>
 * Note Be careful if ititle is generated dynamically since it is passed through tra() by default, use itra to override<br>
 */
function smarty_function_smartlink( $params, &$gBitSmarty ) {
	if( !empty( $params['ihash'] ) ) {
		$hash = array_merge( $params['ihash'], $params );
		$hash['ihash'] = NULL;
	} else {
		// maybe params were passed in separately
		$hash = &$params;
	}

	if( !isset( $hash['ititle'] ) ) {
		return 'You need to supply "ititle" for {smartlink} to work.';
	}

	// work out what the url is
	if( !empty( $hash['iurl'] ) ) {
		$url = $hash['iurl'];
	} elseif( !empty( $hash['ifile'] ) ) {
		if( !empty( $hash['ipackage'] ) ) {
			if( $hash['ipackage'] == 'root' ) {
				$url = BIT_ROOT_URL.$hash['ifile'];
			} else {
				$url = constant( strtoupper( $hash['ipackage'] ).'_PKG_URL' ).$hash['ifile'];
			}
		} else {
			$url = constant( strtoupper( ACTIVE_PACKAGE ).'_PKG_URL' ).$hash['ifile'];
		}
	} else {
		$url = $_SERVER['PHP_SELF'];
	}

	$url_params = NULL;
	if( !empty( $hash['itra'] ) || $hash['itra'] === FALSE ) {
		$ititle = $hash['ititle'];
		$iatitle =  empty( $hash['iatitle'] ) ? $ititle : $hash['iatitle'];
	} else {
		$ititle = tra( $hash['ititle'] );
		$iatitle =  empty( $hash['iatitle'] ) ? $ititle : tra ( $hash['iatitle'] );
	}

	$atitle = 'title="'.$iatitle.'"';

	// if isort is set, we need to deal with all the sorting stuff
	if( !empty( $hash['isort'] ) ) {
		$isort_mode = isset( $hash['isort_mode'] ) ? $hash['isort_mode'] : isset( $_REQUEST['sort_mode'] ) ? $_REQUEST['sort_mode'] : NULL ;
		$sort_asc = $hash['isort'].'_asc';
		$sort_desc = $hash['isort'].'_desc';

		$atitle = 'title="'.tra( 'Sort by' ).": ".$iatitle.'"';
		$url .= '?';
		$url_params .= 'sort_mode=';

		// check if we have to highlight this link, when $isort_mode isn't set
		if( isset( $hash['idefault'] ) && empty( $isort_mode ) ) {
			$isort_mode .= $hash['isort'].'_'.( isset( $hash['iorder'] ) ? $hash['iorder'] : 'asc' );
		}

		// check if sort_mode has anything to do with our link
		if( $sort_asc == $isort_mode ) {
			$sorticon = array(
				'ipackage' => 'icons',
				'iname' => 'view-sort-ascending',
				'iexplain' => 'ascending',
				'iforce' => 'icon',
			);
			$url_params .= $sort_desc;
		} elseif( $sort_desc == $isort_mode ) {
			$sorticon = array(
				'ipackage' => 'icons',
				'iname' => 'view-sort-descending',
				'iexplain' => 'descending',
				'iforce' => 'icon',
			);
			$url_params .= $sort_asc;
		} else {
			$url_params .= $hash['isort'].'_'.( isset( $hash['iorder'] ) ? $hash['iorder'] : 'asc' );
		}
	}

	$ignore = array( 'iatitle', 'icontrol', 'isort', 'ianchor', 'isort_mode', 'iorder', 'ititle', 'idefault', 'ifile', 'ipackage', 'itype', 'iurl', 'ionclick', 'ibiticon', 'iforce', 'itra' );
	// append any other paramters that were passed in
	foreach( $hash as $key => $val ) {
		if( !empty( $val ) && !in_array( $key, $ignore ) ) {
			// normally the key is a string
			if( !is_array( $val ) ){
				$url_params .= empty( $url_params ) ? '?' : '&amp;';
				$url_params .= $key."=".$val;
			// but sometimes it can be an array
			}else{
				foreach( $val as $v ){
					$url_params .= empty( $url_params ) ? '?' : '&amp;';
					$url_params .= $key."[]=".$v;
				}
			}
		}
	}

	if( !empty( $hash['icontrol'] ) && is_array( $hash['icontrol'] ) ) {
		$sep = empty( $url_params ) ? '?' : '&amp;';
		$url_params .= !empty( $hash['icontrol']['current_page'] ) ? $sep.'list_page='.$hash['icontrol']['current_page'] : '';
		$sep = empty( $url_params ) ? '?' : '&amp;';
		$url_params .= !empty( $hash['icontrol']['find'] ) ? $sep.'find='.$hash['icontrol']['find'] : '';
		if( !empty( $hash['icontrol']['parameters'] ) && is_array( $hash['icontrol']['parameters'] ) ) {
			foreach( $hash['icontrol']['parameters'] as $key => $value ) {
				if( !empty( $value )) {
					$sep = empty( $url_params ) ? '?' : '&amp;';
					$url_params .= $sep.$key."=".$value;
				}
			}
		}
	}

	// encode quote marks so we not break href="" construction
	$url_params = preg_replace('/"/', '%22', $url_params);

	require_once $gBitSmarty->_get_plugin_filepath( 'function','biticon' );

	if( isset( $hash['itype'] ) && $hash['itype'] == 'url' ) {
		$ret = $url.$url_params;
	} else {
		$ret = '<a '.$atitle.' '.( !empty( $params['ionclick'] ) ? 'onclick="'.$params['ionclick'].'" ' : '' ).'href="'.$url.$url_params.( !empty( $params['ianchor'] ) ? '#'.$params['ianchor'] : '' ).'">';

		// if we want to display an icon instead of text, do that
		if( isset( $hash['ibiticon'] ) ) {
			$tmp = explode( '/', $hash['ibiticon'] );
			if( !empty( $tmp[2] )) {
				$tmp[1] .= "/".$tmp[2];
			}
			$ibiticon = array(
				'ipackage' => $tmp[0],
				'iname' => $tmp[1],
				'iexplain' => $hash['ititle'], // use untranslated ititle - biticon has a tra()
			);
			if( !empty( $hash['iforce'] ) ) {
				$ibiticon['iforce'] = $hash['iforce'];
			}
			$ret .= smarty_function_biticon( $ibiticon, $gBitSmarty );
		} else {
			$ret .= $ititle;
		}

		if( isset( $sorticon ) ) {
			$ret .= '&nbsp;'.smarty_function_biticon( $sorticon, $gBitSmarty );
		}
		$ret .= '</a>';
	}
	if( isset( $params['itype'] ) && $params['itype'] == 'li' ) {
		$ret = '<li>'.$ret.'</li>';
	}
	return $ret;
}
?>
