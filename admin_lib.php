<?php
/**
 * Administration Library
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/admin_lib.php,v 1.1.1.1.2.5 2005/08/06 18:31:27 lsces Exp $
 */
 
/**
 * Administration Library
 *
 * @package kernel
 */
class AdminLib extends BitBase {
	function AdminLib() {
		BitBase::BitBase();
	}

	function list_dsn($offset, $maxRecords, $sort_mode, $find) {
		
		$bindvars=array();
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`dsn` like ?)";
			$bindvars[]=$findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_dsn` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_dsn` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_dsn($dsn_id, $dsn, $name) {
		// Check the name
		$bindvars=array($name,$dsn_id);
		if ($dsn_id) {
			$query = "update `".BIT_DB_PREFIX."tiki_dsn` set `dsn`='$dsn',`name`=? where `dsn_id`=?";
			$result = $this->query($query,$bindvars);
		} else {
			$query = "delete from `".BIT_DB_PREFIX."tiki_dsn`where `name`=? and `dsn`=?";
			$result = $this->query($query,$bindvars);
			$query = "insert into `".BIT_DB_PREFIX."tiki_dsn`(`name`,`dsn`)
                		values(?,?)";
			$result = $this->query($query,$bindvars);
		}

		// And now replace the perm if not created
		$perm_name = 'bit_p_dsn_' . $name;
		$query = "delete from `".BIT_DB_PREFIX."users_permissions` where `perm_name`=?";
		$this->query($query,array($perm_name));
		$query = "insert into `".BIT_DB_PREFIX."users_permissions`(`perm_name`,`perm_desc`,`type`,`level`) values
    			(?,?,?,?)";
		$this->query($query,array($perm_name,'Can use dsn $dsn','dsn','editor'));
		return true;
	}

	function remove_dsn($dsn_id) {
		$info = $this->get_dsn($dsn_id);

		$perm_name = 'bit_p_dsn_' . $info['name'];
		$query = "delete from `".BIT_DB_PREFIX."users_permissions` where `perm_name`=?";
		$this->query($query,array($perm_name));
		$query = "delete from `".BIT_DB_PREFIX."tiki_dsn` where `dsn_id`=?";
		$this->query($query,array($dsn_id));
		return true;
	}

	function get_dsn($dsn_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_dsn` where `dsn_id`=?";

		$result = $this->query($query,array($dsn_id));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function list_extwiki($offset, $maxRecords, $sort_mode, $find) {
		$bindvars=array();
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`extwiki` like ? )";
			$bindvars[]=$findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_extwiki` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_extwiki` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_extwiki($extwiki_id, $extwiki, $name) {
		// Check the name
		if ($extwiki_id) {
			$query = "update `".BIT_DB_PREFIX."tiki_extwiki` set `extwiki`=?,`name`=? where `extwiki_id`=?";
			$result = $this->query($query,array($extwiki,$name,$extwiki_id));
		} else {
			$query = "delete from `".BIT_DB_PREFIX."tiki_extwiki` where `name`=? and `extwiki`=?";
			$bindvars=array($name,$extwiki);
			$result = $this->query($query,$bindvars);
			$query = "insert into `".BIT_DB_PREFIX."tiki_extwiki`(`name`,`extwiki`)
                		values(?,?)";
			$result = $this->query($query,$bindvars);
		}

		// And now replace the perm if not created
		$perm_name = 'bit_p_extwiki_' . $name;
		$query = "delete from `".BIT_DB_PREFIX."users_permissions`where `perm_name`=?";
		$this->query($query,array($perm_name));
		$query = "insert into `".BIT_DB_PREFIX."users_permissions`(`perm_name`,`perm_desc`,`type`,`level`) values
    			(?,?,?,?)";
		$this->query($query,array($perm_name,'Can use extwiki $extwiki','extwiki','editor'));
		return true;
	}

	function remove_extwiki($extwiki_id) {
		$info = $this->get_extwiki($extwiki_id);

		$perm_name = 'bit_p_extwiki_' . $info['name'];
		$query = "delete from `".BIT_DB_PREFIX."users_permissions` where `perm_name`=?";
		$this->query($query,array($perm_name));
		$query = "delete from `".BIT_DB_PREFIX."tiki_extwiki` where `extwiki_id`=?";
		$this->query($query,array($extwiki_id));
		return true;
	}

	function get_extwiki($extwiki_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_extwiki` where `extwiki_id`=?";

		$result = $this->query($query,array($extwiki_id));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function remove_orphan_images() {
		$merge = array();

		// Find images in tiki_pages
		$query = "select `data` from `".BIT_DB_PREFIX."tiki_pages`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["data"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["data"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["data"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in Tiki articles
		$query = "select `body` from `".BIT_DB_PREFIX."tiki_articles`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["body"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["body"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["body"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in tiki_submissions
		$query = "select `body` from `".BIT_DB_PREFIX."tiki_submissions`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["body"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["body"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["body"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in tiki_blog_posts
		$query = "select `data` from `".BIT_DB_PREFIX."tiki_blog_posts`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["data"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["data"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["data"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		$positives = array();

		foreach ($merge as $img) {
			if (strstr($img, 'show_image')) {
				preg_match("/id=([0-9]+)/", $img, $rq);

				$positives[] = $rq[1];
			}
		}

		$query = "select `image_id` from `".BIT_DB_PREFIX."tiki_images` where `gallery_id`=0";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			$id = $res["image_id"];

			if (!in_array($id, $positives)) {
				$this->remove_image($id);
			}
		}
	}

	function tag_exists($tag) {
		$query = "select distinct `tag_name` from `".BIT_DB_PREFIX."tiki_tags` where `tag_name` = ?";

		$result = $this->query($query,array($tag));
		return $result->numRows($result);
	}

	function remove_tag($tagname) {
		global $wikiHomePage, $gBitUser;

		$this->StartTrans();
		$query = "delete from `".BIT_DB_PREFIX."tiki_tags` where `tag_name`=?";
		$result = $this->query($query,array($tagname));
		$action = "removed tag: $tagname";
		$t = date("U");
		$homePageId = $this->getOne( "SELECT `page_id` from `".BIT_DB_PREFIX."tiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON(tp.`content_id`=tc.`content_id`) WHERE tc.`title`=?", array( $wikiHomePage ) );
		$query = "insert into `".BIT_DB_PREFIX."tiki_actionlog` (`page_id`, `action`, `page_name`, `last_modified`, `user_id`, `ip`, `comment`) values ( ?,?,?,?,?,?,? )";
		$result = $this->query($query,array($homePageId, $action,$wikiHomePage,$t,$gBitUser->mUserId,$_SERVER["REMOTE_ADDR"],''));
		$this->CompleteTrans();
		return true;
	}

	function get_tags() {
		$query = "select distinct `tag_name` from `".BIT_DB_PREFIX."tiki_tags`";

		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res["tag_name"];
		}

		return $ret;
	}

	// This function can be used to store the set of actual pages in the "tags"
	// table preserving the state of the wiki under a tag name.
	function create_tag($tagname, $comment = '') {
		global $wikiHomePage, $gBitUser;

		$this->StartTrans();
		$query = "select * from `".BIT_DB_PREFIX."tiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tp.`content_id`=tc.`content_id` )";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			$data = $res["data"];
			$description = $res["description"];
			$query = "delete from `".BIT_DB_PREFIX."tiki_tags`where `tag_name`=? and `page_id`=?";
			$this->query($query,array($tagname,$res["page_id"]));
			$query = "insert into `".BIT_DB_PREFIX."tiki_tags`(`page_id`,`tag_name`,`page_name`,`hits`,`data`,`last_modified`,`comment`,`version`,`user_id`,`ip`,`flag`,`description`)
                		values(?,?,?,?,?,?,?,?,?,?,?,?)";
			$result2 = $this->query($query,array($res["page_id"],$tagname,$res["title"],$res["hits"],$data,$res["last_modified"],$res["comment"],$res["version"],$res["user_id"],$res["ip"],$res["flag"],$description));
		}

		$homePageId = $this->getOne( "SELECT `page_id` from `".BIT_DB_PREFIX."tiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON(tp.`content_id`=tc.`content_id`) WHERE tc.`title`=?", array( $wikiHomePage ) );
		$action = "created tag: $tagname";
		$t = date("U");
		$query = "insert into `".BIT_DB_PREFIX."tiki_actionlog`(`page_id`,`action`,`page_name`,`last_modified`,`user_id`,`ip`,`comment`) values(?,?,?,?,?,?,?)";
		$result = $this->query($query,array($homePageId,$action,$wikiHomePage,$t,$gBitUser->mUserId,$_SERVER["REMOTE_ADDR"],$comment));
		$this->CompleteTrans();
		return true;
	}

	// This funcion recovers the state of the wiki using a tag_name from the
	// tags table
	function restore_tag($tagname) {
		global $wikiHomePage, $gBitUser;
		require_once( WIKI_PKG_PATH.'BitPage.php' );

		$this->StartTrans();
		$query = "update `".BIT_DB_PREFIX."tiki_pages` set `cache_timestamp`=0";
		$this->query($query,array());
		$query = "select *, `data` AS `edit`, `page_name` AS `title` FROM `".BIT_DB_PREFIX."tiki_tags` where `tag_name`=?";
		$result = $this->query($query,array($tagname));

		while ($res = $result->fetchRow()) {
			$tagPage = new BitPage( $res["page_id"] );
			$tagPage->store( $res );
		}

		$homePageId = $this->getOne( "SELECT `page_id` from `".BIT_DB_PREFIX."tiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON(tp.`content_id`=tc.`content_id`) WHERE tc.`title`=?", array( $wikiHomePage ) );
		$action = "recovered tag: $tagname";
		$t = date("U");
		$query = "insert into `".BIT_DB_PREFIX."tiki_actionlog`(`page_id`, `action`, `page_name`, `last_modified`, `user_id`, `ip`, `comment`) values (?,?,?,?,?,?,?)";
		$result = $this->query($query,array($homePageId,$action,$wikiHomePage,$t, $gBitUser->mUserId,$_SERVER["REMOTE_ADDR"],''));
		$this->CompleteTrans();
		return true;
	}

}

$adminlib = new AdminLib();

?>
