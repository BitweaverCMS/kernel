<?php
/**
 * Communications Library
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/comm_lib.php,v 1.1.1.1.2.6 2005/08/07 16:29:59 lsces Exp $
 */

/**
 * Communications Library
 * Send and receive article content 
 *
 * @package kernel
 */
class CommLib extends BitBase {
	function CommLib() {					BitBase::BitBase();
	}

	function accept_page($received_page_id) {
		$info = $this->get_received_page($received_page_id);

		if ($this->pageExists($info["page_name"]))
			return false;

		$now = date("U");
		$this->create_page($info["page_name"], 0, $info["data"], $now, $info["comment"], $info["received_from_user"], $info["received_from_site"], $info["description"]);
		$query = "delete from `".BIT_DB_PREFIX."tiki_received_pages` where `received_page_id`=?";
		$result = $this->mDb->query($query,array((int)$received_page_id));
		return true;
	}

	function accept_article($received_article_id, $topic) {
		$info = $this->get_received_article($received_article_id);

		$this->replace_article($info["title"], $info["author_name"],
			$topic, $info["use_image"], $info["image_name"], $info["image_size"], $info["image_type"], $info["image_data"],
			$info["heading"], $info["body"], $info["publish_date"], $info["expire_date"], $info["author"],
			0, $info["image_x"], $info["image_y"], $info["type"], $info["rating"]);
		$query = "delete from `".BIT_DB_PREFIX."tiki_received_articles` where `received_article_id`=?";
		$result = $this->mDb->query($query,array((int)$received_article_id));
		return true;
	}

	function list_received_articles($offset, $maxRecords, $sort_mode = 'publish_date_desc', $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%' . strtoupper( $find ) . '%';
			$mid = " where (UPPER(`heading`) like ? or UPPER(`title`) like ? or UPPER(`body`) like ?)";
			$bindvars[] = array( $findesc, $findesc, $findesc );
		} else {
			$mid = "";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_received_articles` $mid order by ".$this->mDb->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_received_articles` $mid";
		$result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_received_page($received_page_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_received_pages` where `received_page_id`=?";
		$result = $this->mDb->query($query,array((int)$received_page_id));
	}

	function remove_received_article($received_article_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_received_articles` where `received_article_id`=?";
		$result = $this->mDb->query($query,array((int)$received_article_id));
	}

	function rename_received_page($received_page_id, $name) {
		$query = "update `".BIT_DB_PREFIX."tiki_received_pages` set `page_name`=? where `received_page_id`=?";
		$result = $this->mDb->query($query,array($name,(int)$received_page_id));
	}

	function get_received_page($received_page_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_received_pages` where `received_page_id`=?";
		$result = $this->mDb->query($query,array((int)$received_page_id));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_received_article($received_article_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_received_articles` where `received_article_id`=?";
		$result = $this->mDb->query($query,array((int)$received_article_id));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function update_received_article($received_article_id, $title, $author_name, $use_image, $image_x, $image_y, $publish_date, $expire_date, $heading, $body, $type, $rating) {
		$size = strlen($body);
		$hash = md5($title . $heading . $body);
		$query = "update `".BIT_DB_PREFIX."tiki_received_articles` set `title`=?, `author_name`=?, `heading`=?, `body`=?, `size`=?, `hash`=?, `use_image`=?, `image_x`=?, ";
		$query.= " `image_y`=?, `publish_date`=?, `expire_date`=?, `type`=?, `rating`=?  where `received_article_id`=?";
		$result = $this->mDb->query($query,
			array($title,$author_name,$heading,$body,(int)$size,$hash,$use_image,(int)$image_x,(int)$image_y,(int)$publish_date,$expire_date,$type,(int)$rating,(int)$received_article_id));
	}

	function update_received_page($received_page_id, $page_name, $data, $comment) {
		$query = "update `".BIT_DB_PREFIX."tiki_received_pages` set `page_name`=?, `data`=?, `comment`=? where `received_page_id`=?";
		$result = $this->mDb->query($query,array($page_name,$data,$comment,(int)$received_page_id));
	}

	function receive_article($site, $user, $title, $author_name, $size, $use_image, $image_name, $image_type, $image_size, $image_x,
		$image_y, $image_data, $publish_date, $expire_date, $created, $heading, $body, $hash, $author, $type, $rating) {
		$now = date("U");
		$query = "delete from `".BIT_DB_PREFIX."tiki_received_articles` where `title`=? and `receivedFromSite`=? and `received_from_user`=?";
		$result = $this->mDb->query($query,array($title,$site,$user));
		$query = "insert into `".BIT_DB_PREFIX."tiki_received_articles`(`received_date`,`received_from_site`,`received_from_user`,`title`,`author_name`,`size`, ";
		$query.= " `use_image`,`image_name`,`image_type`,`image_size`,`image_x`,`image_y`,`image_data`,`publish_date`,`expire_date`,`created`,`heading`,`body`,`hash`,`author`,`type`,`rating`) ";
    $query.= " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$result = $this->mDb->query($query,array((int)$now,$site,$user,$title,$author_name,(int)$size,$use_image,$image_name,$image_type,$image_size,
		                              $image_x,$image_y,$image_data,(int)$publish_date,(int)$expire_date,(int)$created,$heading,$body,$hash,$author,$type,(int)$rating));
	}

	function receive_page($page_name, $data, $comment, $site, $user, $description) {
		$now = date("U");
		// Remove previous page sent from the same site-user (an update)
		$query = "delete from `".BIT_DB_PREFIX."tiki_received_pages` where `page_name`=? and `receivedFromSite`=? and `received_from_user`=?";
		$result = $this->mDb->query($query,array($page_name,$site,$user));
		// Now insert the page
		$query = "insert into `".BIT_DB_PREFIX."tiki_received_pages`(`page_name`,`data`,`comment`,`received_from_site`, `received_from_user`, `received_date`,`description`) values(?,?,?,?,?,?,?)";
		$result = $this->mDb->query($query,array($page_name,$data,$comment,$site,$user,(int)$now,$description));
	}

// Functions for the communication center end ////
}
global $commlib;
$commlib = new CommLib();

?>
