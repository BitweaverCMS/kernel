<?php
/**
 * Content Ranking Library
 * 
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/rank_lib.php,v 1.8 2006/02/02 09:24:22 squareing Exp $
 * @package kernel
 */

/**
 * Content Ranking Library
 * 
 * @package kernel
 * @todo This needs reviewing and the legacy dependencies removed - still using tiki specific tables 
 */
class RankLib extends BitBase {
	function RankLib() {				
		BitBase::BitBase();
	}

	function contentRanking( $pListHash ) {
		$ret = array();
		$bindVars = array();

		$limit = !empty( $pListHash['limit'] ) ? $pListHash['limit'] : 10;
		$sort_mode = !empty( $pListHash['sort_mode'] ) ? $pListHash['sort_mode'] : 'hits_desc';

		if( !empty( $pListHash['content_type_guid'] ) ) {
			$where = "WHERE lc.`content_type_guid`=?";
			$bindVars[] = $pListHash['content_type_guid'];
		}

		$query = "SELECT lc.`content_id`, lc.`title`, lc.`hits` FROM `".BIT_DB_PREFIX."liberty_content` lc $where ORDER BY ".$this->mDb->convert_sortmode( $sort_mode );
		$result = $this->mDb->query( $query, $bindVars, $limit, 0 );

		$_ret = array();
		while( $res = $result->fetchRow() ) {
			$aux["name"] = $res["title"];
			$aux["hits"] = $res["hits"];
			$aux["href"] = BIT_ROOT_URL.'index.php?content_id='.$res["content_id"];
			$_ret[] = $aux;
		}

		$ret["data"]  = $_ret;
		$ret['title'] = !empty( $pListHash['title'] ) ? $pListHash['title'] : tra( "Content Ranking" );
		$ret['y']     = !empty( $pListHash['attribute'] ) ? $pListHash['attribute'] : tra( "Hits" );

		return $ret;
	}

	function wiki_ranking_top_pages($limit) {
		$query = "select lc.`title` as `page_name`, `hits` from `".BIT_DB_PREFIX."wiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = tp.`content_id`) order by `hits` desc";

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["page_name"];

			$aux["hits"] = $res["hits"];
			$aux["href"] = WIKI_PKG_URL.'index.php?page=' . $res["page_name"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top pages");
		$retval["y"] = tra("Hits");
		return $retval;
	}

	function wiki_ranking_top_pagerank($limit) {
		// I don't think this is needed any more - bigbug
		// $this->page_rank();

		$query = "select lc.`title` as `page_name`, `page_rank` from `".BIT_DB_PREFIX."wiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = tp.`content_id`) order by `page_rank` desc";
		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["page_name"];

			$aux["hits"] = $res["page_rank"];
			$aux["href"] = WIKI_PKG_URL.'index.php?page=' . $res["page_name"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Most relevant pages");
		$retval["y"] = tra("Relevance");
		return $retval;
	}

	function wiki_ranking_last_pages($limit) {
		global $gBitSystem;

		$query = "select lc.`title` as `page_name`, `last_modified`, `hits` from `".BIT_DB_PREFIX."wiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = tp.`content_id`) order by `last_modified` desc";

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["page_name"];

			$aux["hits"] = $gBitSystem->get_long_datetime($res["last_modified"]);
			$aux["href"] = WIKI_PKG_URL.'index.php?page=' . $res["page_name"];
			$ret[] = $aux;
		}

		$ret["data"] = $ret;
		$ret["title"] = tra("Wiki last pages");
		$ret["y"] = tra("Modified");
		return $ret;
	}

	function cms_ranking_top_articles($limit) {
		$query = "select lc.`title`, lc.`hits` from `".BIT_DB_PREFIX."articles` tp INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = tp.`content_id`) order by `hits` desc";

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["page_name"];

			$aux["hits"] = $res["hits"];
			$aux["href"] = WIKI_PKG_URL.'index.php?page=' . $res["page_name"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top pages");
		$retval["y"] = tra("Hits");
		return $retval;

		$query = "select * from `".BIT_DB_PREFIX."articles` order by `reads` desc";

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["title"];

			$aux["hits"] = $res["reads"];
			$aux["href"] = ARTICLES_PKG_URL.'read.php?article_id=' . $res["article_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top articles");
		$retval["y"] = tra("Reads");
		return $retval;
	}

	function blog_ranking_top_blogs($limit) {
		$query = "select * from `".BIT_DB_PREFIX."blogs` order by `hits` desc";

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["title"];

			$aux["hits"] = $res["hits"];
			$aux["href"] = BLOGS_PKG_URL.'view.php?blog_id=' . $res["blog_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Most visited blogs");
		$retval["y"] = tra("Visits");
		return $retval;
	}

	function blog_ranking_top_active_blogs($limit) {
		$query = "select * from `".BIT_DB_PREFIX."blogs` order by `activity` desc";

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["title"];

			$aux["hits"] = $res["activity"];
			$aux["href"] = BLOGS_PKG_URL.'view.php?blog_id=' . $res["blog_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Most active blogs");
		$retval["y"] = tra("Activity");
		return $retval;
	}

	function blog_ranking_last_posts($limit) {
		global $gBitSystem;
		$query = "select * from `".BIT_DB_PREFIX."blog_posts` order by `post_id` desc";

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$q = "select title, created from `".BIT_DB_PREFIX."blogs` where `blog_id`=";
			$q.= $res["blog_id"];
			$result2 = $this->mDb->query($q,array(),$limit,0);
			$res2 = $result2->fetchRow();
			$aux["name"] = $res2["title"];
			$aux["hits"] = $gBitSystem->get_long_datetime($res2["created"]);
			$aux["href"] = BLOGS_PKG_URL.'view.php?blog_id=' . $res["blog_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Blogs last posts");
		$retval["y"] = tra("Post date");
		return $retval;
	}

	function wiki_ranking_top_authors($limit) {
		
		$query = "select distinct users.`login` as `user`, count(*) as `numb` from `".BIT_DB_PREFIX."users_users` users INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`user_id` =  users.`user_id`) where lc.`content_type_guid`='".BITPAGE_CONTENT_TYPE_GUID."' group by `user` order by ".$this->mDb->convert_sortmode("numb_desc");

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();
		$retu = array();

		while ($res = $result->fetchRow()) {
			$ret["name"] = $res["user"];
			$ret["hits"] = $res["numb"];
			$ret["href"] = USERS_PKG_URL."index.php?home=".urlencode($res["user"]);
			$retu[] = $ret;
		}
		$retval["data"] = $retu;
		$retval["title"] = tra("Wiki top authors");
		$retval["y"] = tra("Pages");
		return $retval;
	}

	function cms_ranking_top_authors($limit) {
		$query = "select distinct `author`, count(*) as `numb` from `".BIT_DB_PREFIX."articles` group by `author` order by ".$this->mDb->convert_sortmode("numb_desc");

		$result = $this->mDb->query($query,array(),$limit,0);
		$ret = array();
		$retu = array();

		while ($res = $result->fetchRow()) {
			$ret["name"] = $res["author"];
			$ret["hits"] = $res["numb"];
			$ret["href"] = USERS_PKG_URL."index.php?home=".urlencode($res["author"]);
			$retu[] = $ret;
		}
		$retval["data"] = $retu;
		$retval["title"] = tra("Top article authors");
		$retval["y"] = tra("Articles");
		return $retval;
	}
}

$ranklib = new RankLib();

?>
