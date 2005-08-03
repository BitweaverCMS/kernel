<?php
/**
 * User access Banning Library
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/ban_lib.php,v 1.1.1.1.2.4 2005/08/03 16:53:53 lsces Exp $
 */

/**
 * User access Banning Library
 *
 * @package kernel
 */
class BanLib extends BitBase {
	function BanLib() {				
	BitBase::BitBase();
	}

	function get_rule($ban_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_banning` where `ban_id`=?";

		$result = $this->query($query,array($ban_id));
		$res = $result->fetchRow();
		$aux = array();
		$query2 = "select `section` from `".BIT_DB_PREFIX."tiki_banning_sections` where `ban_id`=?";
		$result2 = $this->query($query2,array($ban_id));
		$aux = array();

		while ($res2 = $result2->fetchRow()) {
			$aux[] = $res2['section'];
		}

		$res['sections'] = $aux;
		return $res;
	}

	function remove_rule($ban_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_banning` where `ban_id`=?";

		$this->query($query,array($ban_id));
		$query = "delete from `".BIT_DB_PREFIX."tiki_banning_sections` where `ban_id`=?";
		$this->query($query,array($ban_id));
	}

	function list_rules($offset, $maxRecords, $sort_mode, $find, $where = '') {

		if ($find) {
			$findesc = '%' . strtoupper( $find ) . '%';
			$mid = " where ((UPPER(`message`) like ?) or (UPPER(`title`) like ?))";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		// DB abstraction: TODO
		if ($where) {
			if ($mid) {
				$mid .= " and ($where) ";
			} else {
				$mid = "where ($where) ";
			}
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_banning` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_banning` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$query2 = "select * from `".BIT_DB_PREFIX."tiki_banning_sections` where `ban_id`=?";
			$result2 = $this->query($query2,array($res['ban_id']));

			while ($res2 = $result2->fetchRow()) {
				$aux[] = $res2;
			}

			$res['sections'] = $aux;
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		$now = date("U");
		$query = "select `ban_id` from `".BIT_DB_PREFIX."tiki_banning` where `use_dates`=? and `date_to` < ?";
		$result = $this->query($query,array('y',$now));

		while ($res = $result->fetchRow()) {
			$this->remove_rule($res['ban_id']);
		}

		return $retval;
	}

	/*
	ban_id integer(12) not null auto_increment,
	  mode enum('user','ip'),
	  title varchar(200),
	  ip1 integer(3),
	  ip2 integer(3),
	  ip3 integer(3),
	  ip4 integer(3),
	  user varchar(200),
	  date_from timestamp,
	  date_to timestamp,
	  use_dates char(1),
	  message text,
	  primary key(ban_id)
	  */
	function replace_rule($ban_id, $mode, $title, $ip1, $ip2, $ip3, $ip4, $user, $date_from, $date_to, $use_dates, $message,
		$sections) {

		if ($ban_id) {
			$query = " update `".BIT_DB_PREFIX."tiki_banning` set
  			`title`=?,
  			`ip1`=?,
  			`ip2`=?,
  			`ip3`=?,
  			`ip4`=?,
  			`user`=?,
  			`date_from` = ?,
  			`date_to` = ?,
  			`use_dates` = ?,
  			`message` = ?
  			where `ban_id`=?
  		";

			$this->query($query,array($title,$ip1,$ip2,$ip3,$ip4,$user,$date_from,$date_to,$use_dates,$message,$ban_id));
		} else {
			$now = date("U");

			$query = "insert into `".BIT_DB_PREFIX."tiki_banning`(`mode`,`title`,`ip1`,`ip2`,`ip3`,`ip4`,`user`,`date_from`,`date_to`,`use_dates`,`message`,`created`)
		values(?,?,?,?,?,?,?,?,?,?,?,?)";
			$this->query($query,array($mode,$title,$ip1,$ip2,$ip3,$ip4,$user,$date_from,$date_to,$use_dates,$message,$now));
			$ban_id = $this->getOne("select max(`ban_id`) from `".BIT_DB_PREFIX."tiki_banning` where `created`=?",array($now));
		}

		$query = "delete from `".BIT_DB_PREFIX."tiki_banning_sections` where `ban_id`=?";
		$this->query($query,array($ban_id));

		foreach ($sections as $section) {
			$query = "insert into `".BIT_DB_PREFIX."tiki_banning_sections`(`ban_id`,`section`) values(?,?)";

			$this->query($query,array($ban_id,$section));
		}
	}
}

$banlib = new BanLib();

?>
