<?php
/**
 * Menu Management Library
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/menu_lib.php,v 1.5 2005/08/24 20:52:14 squareing Exp $
 */

/**
 * tiki format Menu Management Library
 *
 * @package kernel
 */
class MenuLib extends BitBase {
	function MenuLib() {
		BitBase::BitBase();
	}

	function get_menu($menu_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_menus` where `menu_id`=?";
		$result = $this->mDb->query($query,array((int)$menu_id));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function list_menus($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . strtoupper( $find ) . '%';

			$mid = " where (UPPER(`name`) like ? or UPPER(`description`) like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_menus` $mid order by ".$this->mDb->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_menus` $mid";
		$result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query = "select count(*) from `".BIT_DB_PREFIX."tiki_menu_options` where `menu_id`=?";
			$res["options"] = $this->mDb->getOne($query,array((int)$res["menu_id"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_menu($menu_id, $name, $description, $type) {
		// Check the name
		if (isset($menu_id) and $menu_id > 0) {
			$query = "update `".BIT_DB_PREFIX."tiki_menus` set `name`=?,`description`=?,`type`=? where `menu_id`=?";
			$bindvars=array($name,$description,$type,(int)$menu_id);
		} else {
			// was: replace into. probably we need a delete here
			$query = "insert into `".BIT_DB_PREFIX."tiki_menus`(`name`,`description`,`type`) values(?,?,?)";
			$bindvars=array($name,$description,$type);
		}

		$result = $this->mDb->query($query,$bindvars);
		return true;
	}

	function get_max_option($menu_id) {
		$query = "select max(`position`) from `".BIT_DB_PREFIX."tiki_menu_options` where `menu_id`=?";

		$max = $this->mDb->getOne($query,array((int)$menu_id));
		return $max;
	}

	function replace_menu_option($menu_id, $option_id, $name, $url, $type, $position, $section, $perm, $groupname) {
		if ($option_id) {
			$query = "update `".BIT_DB_PREFIX."tiki_menu_options` set `name`=?,`url`=?,`type`=?,`position`=?,`section`=?,`perm`=?,`groupname`=?  where `option_id`=?";
			$bindvars=array($name,$url,$type,(int)$position,$section,$perm,$groupname,$option_id);
		} else {
			$query = "insert into `".BIT_DB_PREFIX."tiki_menu_options`(`menu_id`,`name`,`url`,`type`,`position`,`section`,`perm`,`groupname`) values(?,?,?,?,?,?,?,?)";
			$bindvars=array((int)$menu_id,$name,$url,$type,(int)$position,$section,$perm,$groupname);
		}

		$result = $this->mDb->query($query, $bindvars);
		return true;
	}

	function remove_menu($menu_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_menus` where `menu_id`=?";

		$result = $this->mDb->query($query,array((int)$menu_id));
		$query = "delete from `".BIT_DB_PREFIX."tiki_menu_options` where `menu_id`=?";
		$result = $this->mDb->query($query,array((int)$menu_id));
		return true;
	}

	function remove_menu_option($option_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_menu_options` where `option_id`=?";

		$result = $this->mDb->query($query,array((int)$option_id));
		return true;
	}

	function get_menu_option($option_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_menu_options` where `option_id`=?";

		$result = $this->mDb->query($query,array((int)$option_id));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function list_menu_options($menu_id, $offset, $maxRecords, $sort_mode, $find, $full=false) {
		global $gBitSmarty,$gBitUser;
		$ret = array();
		$retval = array();
		$bindvars = array((int)$menu_id);
		$usergroups = $gBitUser->getGroups();
		if ($find) {
			$mid = " where `menu_id`=? and (UPPER(`name`) like ? or UPPER(`url`) like ?)";
			$bindvars[] = '%'. strtoupper( $find ) . '%';
			$bindvars[] = '%'. strtoupper( $find ) . '%';
		} else {
			$mid = " where `menu_id`=? ";
		}
		$query = "select * from `".BIT_DB_PREFIX."tiki_menu_options` $mid order by ".$this->mDb->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_menu_options` $mid";
		$result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindvars);
		while ($res = $result->fetchRow()) {
			if (!$full) {
				$display = true;
				if (isset($res['section']) and $res['section']) {
					$sections = split(",",$res['section']);
					foreach ($sections as $sec) {
						if (!isset($gBitSmarty->_tpl_vars["$sec"]) or $gBitSmarty->_tpl_vars["$sec"] != 'y') {
							$display = false;
							break;
						}
					}
				}
				if ($display) {
					if (isset($res['perm']) and $res['perm']) {
						$sections = split(",",$res['perm']);
						foreach ($sections as $sec) {
							if (!isset($gBitSmarty->_tpl_vars["$sec"]) or $gBitSmarty->_tpl_vars["$sec"] != 'y') {
								$display = false;
								break;
							}
						}
					}
				}
				if ($display) {
					if (isset($res['groupname']) and $res['groupname']) {
						$sections = split(",",$res['groupname']);
						foreach ($sections as $sec) {
							if ($sec and !in_array($sec,$usergroups)) {
								$display = false;
							}
						}
					}
				}
				if ($display) {
					$pos = $res['position'];
					$ret["$pos"] = $res;
				}
			} else {
				$ret[] = $res;
			}
		}
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}
	// Menubuilder ends ////

}

global $menulib;
$menulib = new MenuLib();

?>
