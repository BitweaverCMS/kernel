<?php
/**
 * Modules Management Library 
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/mod_lib.php,v 1.1.1.1.2.7 2005/08/03 16:53:48 lsces Exp $
 */

/**
 * Modules Management Library 
 *
 * @package kernel
 */
class ModLib extends BitBase {
	function ModLib() {
		BitBase::BitBase();
	}

	function replace_user_module($name, $title, $data) {

		if ((!empty($name)) && (!empty($title)) && (!empty($data))) {
			$query = "delete from `".BIT_DB_PREFIX."tiki_user_modules` where `name`=?";
			$result = $this->query($query,array($name));
			$query = "insert into `".BIT_DB_PREFIX."tiki_user_modules`(`name`,`title`,`data`) values(?,?,?)";

			$result = $this->query($query,array($name,$title,$data));
			return true;
		}
	}

	function verifyModuleParams( &$pHash ) {
		if( empty( $pHash['availability'] ) ) $pHash['availability'] = NULL;
		if( empty( $pHash['params'] ) ) $pHash['params'] = NULL;
		if( empty( $pHash['title'] ) ) $pHash['title'] = NULL;
		if( empty( $pHash['rows'] ) ) {
			$pHash['rows'] = NULL;
		} else {
			$pHash['rows'] = is_numeric($pHash['rows']) ? $pHash['rows'] : 10;
		}
		if( empty( $pHash['cache_time'] ) ) {
			$pHash['cache_time'] = NULL;
		} else {
			$pHash['cache_time'] = is_numeric($pHash['cache_time']) ? $pHash['cache_time'] : 0;
		}
		if( empty( $pHash['type'] ) ) $pHash['type'] = NULL;
		if( empty( $pHash['groups'] ) ) {
			$pHash['groups'] = NULL;
		} elseif( is_array( $pHash['groups'] ) ) {
			$pHash['groups'] = implode( ' ', $pHash['groups'] );
		}


		if( empty( $pHash['module_id'] ) || !is_numeric( $pHash['module_id'] ) ) {
			$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."tiki_module_map` WHERE `module_rsrc`=?";
			$result = $this->query( $query, array( $pHash['module_rsrc'] ) );
			$pHash['module_id'] = $result->fields['module_id'];
		}

		return TRUE;
	}

	function storeModule( &$pHash ) {
		if( $this->verifyModuleParams( $pHash ) ) {
			$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."tiki_module_map` WHERE `module_rsrc`=?";
			$result = $this->query($query,array($pHash['module_rsrc']));

			if (empty($result->fields)) { 	// If this module is not listed in the module map...
				$query = "INSERT INTO `".BIT_DB_PREFIX."tiki_module_map` (`module_rsrc`) VALUES ( ? )";	// Insert a row for this module
				$result = $this->query($query,array($pHash['module_rsrc']));
				$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."tiki_module_map` WHERE `module_rsrc`=?";	// Get the module_id assigned to it
				$result = $this->query($query,array($pHash['module_rsrc']));
			}
			$pHash['module_id'] = $result->fields['module_id'];

			$query = "SELECT COUNT(*) AS `count` FROM `".BIT_DB_PREFIX."tiki_layouts_modules` WHERE `module_id`=?";
			$result = $this->query($query,array($pHash['module_id']));
			if( empty( $pHash['groups'] ) ) {
				$pHash['groups'] = NULL;
			}

			$bindVars = array( $pHash['availability'], $pHash['title'], $pHash['cache_time'], $pHash['rows'],  $pHash['groups'], $pHash['module_id'] );

			if ( ($result->fields['count']) > 0 ) {
				$query = "UPDATE `".BIT_DB_PREFIX."tiki_layouts_modules`
						  SET `availability`=?, `title`=?, `cache_time`=?, `rows`=?, `groups`=?
						  WHERE `module_id`=?";
			} else {
				$query = "INSERT INTO `".BIT_DB_PREFIX."tiki_layouts_modules`
						  ( `availability`, `title`, `cache_time`, `rows`, `groups`, `module_id` )
						  VALUES ( ?, ?, ?, ?, ?, ? )";
			}
			$result = $this->query( $query, $bindVars );

			if( !isset($pHash['layout']) || $pHash['layout'] == 'kernel' ) {
				$this->query( "UPDATE `".BIT_DB_PREFIX."tiki_layouts_modules` SET `params`=? WHERE `module_id`=?", array( $pHash['params'], $pHash['module_id'] ) );
			}

	// `cache_time`, `groups`, `params`, `rows`, `type`,
	// (int)$cache_time, $params, (int)$rows, $groups, $type,
//			if( isset( $pHash['availability'] ) && ($pHash['availability'] == "D" || $pHash['availability'] == "P") ) {
//				global $usermoduleslib;
//				$usermoduleslib->add_module_users($name,$position,$order,$groups,$params,$type);
//			}
		}

	}

	function verifyLayoutParams( &$pHash ) {
		$ret = TRUE;
		if( empty( $pHash['ord'] ) ) $pHash['ord'] = NULL;

		if( (empty( $pHash['module_id'] ) || !is_numeric( $pHash['module_id'] )) && isset( $pHash['module_rsrc'] ) ) {
			$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."tiki_module_map` WHERE `module_rsrc`=?";
			$result = $this->query( $query, array( $pHash['module_rsrc'] ) );
			$pHash['module_id'] = $result->fields['module_id'];
		}

		if( empty( $pHash['pos'] ) ) {
			$ret = FALSE;
		}

		return $ret;
	}

	function storeLayout( $pHash ) {
		if( $this->verifyLayoutParams( $pHash ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_layouts` WHERE `user_id`=? AND `layout`=? AND `module_id`=?";
			$result = $this->query( $query, array( $pHash['user_id'], $pHash['layout'], (int)$pHash['module_id'] ) );
			//check for valid values
			// kernel layout (site default) params are stored in tiki_layouts_modules
			if( $pHash['layout'] == 'kernel' ) {
				$pHash['params'] = NULL;
			}

			if( !isset( $pHash['params'] ) ) {
				$pHash['params'] = NULL;
			}

			$query = "INSERT INTO `".BIT_DB_PREFIX."tiki_layouts`
					  (`user_id`, `module_id`, `position`, `ord`, `params`, `layout`)
					  VALUES (?,?,?,?,?,?)";
			$result = $this->query( $query, array( $pHash['user_id'], $pHash['module_id'], $pHash['pos'], (int)$pHash['ord'], $pHash['params'], $pHash['layout'] ) );
		}
		return true;
	}

	function getAssignedModules( $name ) {
		$query = "select tlm.*, count() from `".BIT_DB_PREFIX."tiki_modules` where `name`=?";

		$result = $this->query($query,array($name));
		$res = $result->fetchRow();

		// handle old style serialized group names for legacy data
		if( preg_match( '/[A-Za-z]/', $res["groups"] ) ) {
			static $getAllModulesallGroups;
			if( empty( $allGroups ) ) {
				$allGroups = $gBitUser->getAllUserGroups( ROOT_USER_ID );
			}
			$allGroupNames = array();
			foreach( array_keys( $allGroups ) as $groupId ) {
				array_push( $allGroupNames, $allGroups[$groupId] );
			}
			if( $modGroups = @unserialize( $res["groups"] ) ) {
				foreach( $grps as $groupName ) {
					if( $searchId = array_search( $groupName, $allGroupNames ) ) {
						$res["groups"] = $searchId.' ';
					}
				}
			}
		}
		$res["groups"] = explode( trim( $res["groups"] ), ' ' );

		return $res;
	}

	function disableModule( $pModuleName ) {
		$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."tiki_module_map` WHERE `module_rsrc`=?";
		$result = $this->query($query,array($pModuleName));
		if (!empty($result->fields)) {
			$module_id = $result->fields['module_id'];
			$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_layouts_modules` WHERE `module_id`=?";
			$result = $this->query($query,array($module_id));
		}
	}

	function assignModule( $pModuleId, $pUserId, $pLayout, $pPosition, $pOrder = 0, $securityOK = FALSE ) {
		global $bit_p_admin, $gBitUser;
		// security check
		if( ($bit_p_admin || $securityOK || ( $gBitUser->mUserId==$pUserId )) && is_numeric( $pModuleId ) ) {
			$this->unassignModule( $pModuleId, $pUserId, $pLayout );
			$query = "INSERT INTO `".BIT_DB_PREFIX."tiki_layouts` (`user_id`, `module_id`, `layout`, `position`, `ord`) VALUES(?,?,?,?,?)";
			$result = $this->query( $query, array( $pUserId, (int)$pModuleId, $pLayout, $pPosition, $pOrder ) );
		}
	}
	
	function removeAllLayoutModules( $pUserId = NULL, $pLayout = NULL, $securityOK = FALSE) {
		global $bit_p_admin, $gBitUser;
		if ( ($bit_p_admin || $securityOK || ( $gBitUser->mUserId == $pUserId )) && ($pUserId && $pLayout)) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_layouts` WHERE `user_id` = ? AND `layout` = ?";
			$result = $this->query( $query, array($pUserId, $pLayout));
		}
	}
	
	function unassignModule( $pModuleId, $pUserId = NULL, $pLayout = NULL ) {
		global $gBitUser;

		if (!is_numeric($pModuleId)) {
			$pModuleId = $this->get_module_id($pModuleId);
			if (!$pModuleId)
				return FALSE;
		}
		$binds = array((int)$pModuleId);

		if ($pUserId) {
			$userSql = " AND `user_id` = ? ";
			array_push($binds, (int)$pUserId);
		} else {
			$userSql = '';
		}

		if ($pLayout) {
			$layoutSql = " AND `layout` = ? ";
			array_push($binds, $pLayout);
		} else {
			$layoutSql = '';
		}

		// security check
		if( ($gBitUser->isAdmin() || ( $pUserId && $gBitUser->mUserId==$pUserId )) && is_numeric( $pModuleId ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_layouts` where `module_id`=? $userSql $layoutSql";
			$result = $this->query( $query, $binds );
/*
			// count to see if we still have this module in other layouts
			$query = "SELECT COUNT(*) AS exists FROM `".BIT_DB_PREFIX."tiki_layouts` WHERE `name`=?";
			$result = $this->query( $query, array( $pName ) );
			if( !$result->fields['exists'] ) {
				$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_user_assigned_modules` WHERE `name`=?";
				$result = $this->query( $query,array( $pName ) );
			}
*/
		}
		return true;
	}
/*
	function get_rows($mod_rsrc, $user_id = NULL, $default = 5) {
		/ *$query = "select `rows` from `".BIT_DB_PREFIX."tiki_layouts_modules` where `name`=?";

		$rows = $this->getOne($query,array($name));

		if ($rows == 0)
			$rows = 10;* /
		// First we try to get rows setting at the per-user level (e.g. from tiki_layouts table)
		if ($user_id) {
			$query = "SELECT tl.`rows`
				  FROM `".BIT_DB_PREFIX."tiki_layouts` tl, `".BIT_DB_PREFIX."tiki_module_map` tmm
				  WHERE tmm.`module_rsrc` = ? AND tl.`user_id` = ? AND tl.`module_id` = tmm.`module_id`";
			$result = $this->query($query,array($mod_rsrc, $user_id));
		}

		if (!$user_id || !$result->fields['rows']) {
			// No per-user preferences were stored for this user so we will pull the default parameters
			$query = "SELECT tlm.`rows`
				  FROM `".BIT_DB_PREFIX."tiki_layouts_modules` tlm, `".BIT_DB_PREFIX."tiki_module_map` tmm
				  WHERE tmm.`module_rsrc` = ? AND tmm.`module_id` = tlm.`module_id`";
			$result = $this->query($query,array($mod_rsrc));
		}
		$rows = $result->fields['rows'];
		if ($rows <= 0)
			$rows = $default;

		return $rows;
	}
*/
	function store_rows($rows, $mod_rsrc, $user_id = NULL) {
		$module_id = $this->get_module_id($mod_rsrc);

		if (!$module_id)
			return FALSE;

		if ($user_id) {
			$query = "UPDATE `".BIT_DB_PREFIX."tiki_layouts` SET `rows` = ?  WHERE `module_id` = ? AND `user_id` = ?";
			$result = $this->query($query, array($rows, $module_id, $user_id));
		} else {
			$query = "UPDATE `".BIT_DB_PREFIX."tiki_layouts_modules` SET `rows` = ? WHERE `module_id` = ?";
			$result = $this->query($query, array($rows, $module_id));
		}

		return TRUE;
	}

	function moduleUp( $pModuleId, $pUserId, $pLayout ) {
		if( is_numeric( $pModuleId ) ) {
			$query = "update `".BIT_DB_PREFIX."tiki_layouts` SET `ord`=`ord`-1 WHERE `module_id`=? AND `user_id`=? AND `layout`=?";
			$result = $this->query( $query, array( $pModuleId, $pUserId, $pLayout ) );
		}
		return true;
	}

	function moduleDown( $pModuleId, $pUserId, $pLayout ) {
		if( is_numeric( $pModuleId ) ) {
			$query = "UPDATE `".BIT_DB_PREFIX."tiki_layouts` SET `ord`=`ord`+1 WHERE `module_id`=? AND `user_id`=? AND `layout`=?";
			$result = $this->query( $query, array( $pModuleId, $pUserId, $pLayout ) );
		}
		return true;
	}

	function modulePosition( $pModuleId, $pUserId, $pLayout, $pPosition ) {
		if( is_numeric( $pModuleId ) ) {
			$query = "UPDATE `".BIT_DB_PREFIX."tiki_layouts` SET `position`=? WHERE `module_id`=? AND `user_id`=? AND `layout`=?";
			$result = $this->query( $query, array( $pPosition, $pModuleId, $pUserId, $pLayout ) );
		}
	}

	function hasAssignedModules( $iUserMixed ) {
		if( is_numeric( $iUserMixed ) ) {
			$query = "SELECT count(`module_id`) FROM `".BIT_DB_PREFIX."tiki_layouts` where `user_id`=?";
		} else {
			$query = "SELECT count(tl.`module_id`)
					  FROM `".BIT_DB_PREFIX."tiki_layouts` tl, `".BIT_DB_PREFIX."users_users` uu
					  WHERE tl.`user_id`=uu.`user_id` AND uu.`login`=?";
		}
		$result = $this->getOne( $query, array( $iUserMixed ) );

		return $result;
	}


	function getAssignableModules() {
		global $gBitUser;

		$ret = array( 'center'=>array(), 'border'=>array() );
		$query = "SELECT tmm.`module_rsrc`, tlm.*
	              FROM `".BIT_DB_PREFIX."tiki_layouts_modules` tlm, `".BIT_DB_PREFIX."tiki_module_map` tmm
				  WHERE tmm.`module_id` = tlm.`module_id` ORDER BY `module_rsrc`";
		$result = $this->query( $query );
		while( !$result->EOF ) {
			if( preg_match( '/center_/', $result->fields['module_rsrc'] ) ) {
				$subArray = 'center';
			} else {
				$subArray = 'border';
			}
			$result->fields['name'] = $this->convertResourceToName( $result->fields['module_rsrc'] );

			// handle old style serialized group names for legacy data
			if( preg_match( '/[A-Za-z]/', $result->fields["groups"] ) ) {
				static $allGroups;
				if( empty( $allGroups ) ) {
					$allGroups = $gBitUser->getAllUserGroups( ROOT_USER_ID );
				}
				$allGroupNames = array();
				foreach( array_keys( $allGroups ) as $groupId ) {
					array_push( $allGroupNames, $allGroups[$groupId] );
				}
				if( $modGroups = @unserialize( $result->fields["groups"] ) ) {
					foreach( $modGroups as $groupName ) {
						if( $searchId = array_search( $groupName, $allGroupNames ) ) {
							$result->fields["groups"] = $searchId.' ';
						}
					}
				}
			}

			$result->fields["groups"] = trim( $result->fields["groups"] );
			if( !empty( $result->fields["groups"] ) ) {
				$result->fields["groups"] = explode( $result->fields["groups"], ' ' );
			}
			if ( $gBitUser->isAdmin() || !empty( $result->fields['groups'] ) || (is_array($result->fields['groups']) && in_array($gBitUser->mGroups, $result->fields['groups'])) ) {
				array_push( $ret[$subArray], $result->fields );
			}
			$result->MoveNext();
		}
		return $ret;
	}

	function convertResourceToName( $iRsrc ) {
		if( is_string( $iRsrc ) ) {
			// Generate human friendly names
			list($source, $file) = split( '/', $iRsrc );
			list($rsrc, $package) = split( ':', $source );
			$file = str_replace( 'mod_', '', $file );
			$file = str_replace( 'center_', '', $file );
			$file = str_replace( '.tpl', '', $file );
			return( $package.' -> '.str_replace( '_', ' ', $file ) );
		}
	}

	function generateModuleNames( &$p2DHash ) {
		if( is_array( $p2DHash ) ) {
			// Generate human friendly names
			foreach( array_keys( $p2DHash ) as $col ) {
				if( count( $p2DHash[$col] ) ) {
					foreach( array_keys( $p2DHash["$col"] ) as $mod ) {
						list($source, $file) = split( '/', $p2DHash[$col][$mod]['module_rsrc'] );
						@list($rsrc, $package) = split( ':', $source );
						// handle special case for custom modules
						if( !isset( $package ) ) {
							$package = $rsrc;
						}
						$file = str_replace( 'mod_', '', $file );
						$file = str_replace( '.tpl', '', $file );
						$p2DHash[$col][$mod]['name'] = $package.' -> '.str_replace( '_', ' ', $file );
					}
				}
			}
		}
	}


	function getAllModules( $pDir='modules', $pPrefix='mod_' ) {
		global $gBitSystem;
		$user_modules = $this->list_user_modules();

		$all_modules = array();

		if( $pPrefix == 'mod_' ) {
			foreach ($user_modules["data"] as $um) {
				$all_modules[tra( 'Custom Modules' )]['_custom:custom/'.$um["name"]] = $um["name"];
			}
		}

		// iterate through all packages and look for all possible modules
		foreach( array_keys( $gBitSystem->mPackages ) as $key ) {
			$loc = BIT_ROOT_PATH.$gBitSystem->mPackages[$key]['dir'].'/'.$pDir;
			if( @is_dir( $loc ) ) {
				$h = opendir( $loc );
				if( $h ) {
					while (($file = readdir($h)) !== false) {
						if ( preg_match( "/^$pPrefix(.*)\.tpl$/", $file, $match ) ) {
							$all_modules[ucfirst( $key )]['bitpackage:'.$key.'/'.$file] = str_replace( '_', ' ', $match[1] );
						}
					}
					closedir ($h);
				}
			}
			// we scan temp/<pkg>/modules for module files as well for on the fly generated modules (e.g. nexus)
			if( $pDir == 'modules' ) {
				$loc = TEMP_PKG_PATH.$gBitSystem->mPackages[$key]['dir'].'/'.$pDir;
				if( @is_dir( $loc ) ) {
					$h = opendir( $loc );
					if( $h ) {
						while (($file = readdir($h)) !== false) {
							if ( preg_match( "/^$pPrefix(.*)\.tpl$/", $file, $match ) ) {
								$all_modules[ucfirst( $key )]['bitpackage:temp/'.$key.'/'.$file] = str_replace( '_', ' ', $match[1] );
							}
						}
						closedir ($h);
					}
				}
			}
		}

		return $all_modules;
	}



	/*shared*/
	function is_user_module($name) {
		$query = "select `name`  from `".BIT_DB_PREFIX."tiki_user_modules` where `name`=?";
		$result = $this->query($query,array($name));
		return $result->numRows();
	}

	/*shared*/
	function get_user_module($name) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_user_modules` where `name`=?";
		$result = $this->query($query,array($name));
		$res = $result->fetchRow();
		return $res;
	}

	function remove_user_module($name) {
		$moduleId = $this->get_module_id('_custom:custom/'.$name);

		if ($moduleId) {
			$this->unassignModule($moduleId);
			$query = " delete from `".BIT_DB_PREFIX."tiki_user_modules` where `name`=?";
			$result = $this->query($query,array($name));
			$query = " DELETE FROM `".BIT_DB_PREFIX."tiki_layouts_modules` where `module_id` = ?";
			$result = $this->query($query, array($moduleId));
		}

		return true;
	}

	function list_user_modules() {
		$query = "select * from `".BIT_DB_PREFIX."tiki_user_modules`";

		$result = $this->query($query,array());
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_user_modules`";
		$cant = $this->getOne($query_cant,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_module_params($mod_rsrc, $user_id = ROOT_USER_ID ) {
		// First we try to get preferences at the per-user level (e.g. from tiki_layouts table)
		$query = "SELECT tl.`params`, tl.`rows`
			  FROM `".BIT_DB_PREFIX."tiki_layouts` tl, `".BIT_DB_PREFIX."tiki_module_map` tmm
			  WHERE tmm.`module_rsrc` = ? AND tl.`user_id` = ? AND tmm.`module_id` = tl.`module_id`";
		$result = $this->query($query,array($mod_rsrc, $user_id));
		$paramsStr = $result->fields['params'];

		$params = array();

		if (!$paramsStr) {
			// No per-user preferences were stored for this user so we will pull the default parameters
			$query = "SELECT tlm.`params`, tlm.`rows`
				  FROM `".BIT_DB_PREFIX."tiki_layouts_modules` tlm, `".BIT_DB_PREFIX."tiki_module_map` tmm
				  WHERE tmm.`module_rsrc` = ? AND tmm.`module_id` = tlm.`module_id`";
			$result = $this->query($query,array($mod_rsrc));
			$paramsStr = $result->fields['params'];
		}
		if ($paramsStr) {
			$tok = strtok($paramsStr,';');
			while ($tok) {
				$pref = explode('=',$tok);
					if (count($pref) >= 2)
						$params[$pref[0]] = $pref[1];
				$tok = strtok(';');
			}
		}


		$params['rows'] = (!empty($result->fields['rows']) ? (!$result->fields['rows'] ? 10 : $result->fields['rows']) : 10);	// interim hack - drewslater

		return $params;
	}

	function store_module_params($mod_rsrc, $user_id, $params) {
		if (!is_numeric($mod_rsrc))
			$module_id = $this->get_module_id($mod_rsrc);
		else
			$module_id = $mod_rsrc;

		$paramsStr = '';

		foreach ($params as $setting=>$value) {
			$paramsStr .= "$setting=$value;";
		}

		if (!$module_id)
			return FALSE;

		if ($user_id) {
			$query = "UPDATE `".BIT_DB_PREFIX."tiki_layouts` SET `params` = ?  WHERE `module_id` = ? AND `user_id` = ?";
			$result = $this->query($query, array($paramsStr, $module_id, $user_id));
		} else {
			$query = "UPDATE `".BIT_DB_PREFIX."tiki_layouts_modules` SET `params` = ? WHERE `module_id` = ?";
			$result = $this->query($query, array($paramsStr, $module_id));
		}

		return TRUE;
	}

	function get_module_id($mod_rsrc) {
		$query = "SELECT `module_id`
			  FROM `".BIT_DB_PREFIX."tiki_module_map`
			  WHERE `module_rsrc` = ?";
		$result = $this->query($query, array($mod_rsrc));

		$ret = (!empty($result->fields['module_id']) ? $result->fields['module_id'] : NULL);

		return $ret;
	}

	function user_has_module_assigned($iUserId, $iLayout = NULL, $iModuleId = NULL, $iModuleRsrc = NULL) {
		$ret = FALSE;
		if (!$iModuleId && $iModuleRsrc) {
			$iModuleId = $this->get_module_id($iModuleRsrc);
		}

		if ($iModuleId && $iUserId) {
			$bindVars = array($iUserId, $iModuleId);
			$sql = "SELECT count(*) FROM `".BIT_DB_PREFIX."tiki_layouts` WHERE `user_id` = ? AND `module_id` = ?";
			if ($iLayout) {
				$bindVars[] = $iLayout;
				$sql .= " AND `layout` = ?";
			}
			$ret = (bool)$this->getOne($sql, $bindVars);
		}
		return $ret;
	}


}

/**
 * @global ModLib Module library
 */
global $modlib;
$modlib = new ModLib();

?>
