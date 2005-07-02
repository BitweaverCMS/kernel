<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/rank_lib.php,v 1.1.1.1.2.2 2005/07/02 05:33:47 jht001 Exp $
 * @package kernel
 */

/**
 * @package kernel
 * @subpackage RankLib
 */
class RankLib extends BitBase {
	function RankLib() {				
	BitBase::BitBase();
	}
	function wiki_ranking_top_pages($limit) {
		$query = "select tc.`title` as `page_name`, `hits` from `".BIT_DB_PREFIX."tiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = tp.`content_id`) order by `hits` desc";

		$result = $this->query($query,array(),$limit,0);
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

		$query = "select tc.`title` as `page_name`, `page_rank` from `".BIT_DB_PREFIX."tiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = tp.`content_id`) order by `page_rank` desc";
		$result = $this->query($query,array(),$limit,0);
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

		$query = "select tc.`title` as `page_name`, `last_modified`, `hits` from `".BIT_DB_PREFIX."tiki_pages` tp INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = tp.`content_id`) order by `last_modified` desc";

		$result = $this->query($query,array(),$limit,0);
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

	function forums_ranking_last_topics($limit) {
		global $gBitSystem;
		$query = "select * from
		`".BIT_DB_PREFIX."tiki_comments`,`".BIT_DB_PREFIX."tiki_forums` where
		`object`=".$this->sql_cast("`forum_id`","string")." and `object_type` = 'forum' and
		`parent_id`=0 order by `comment_date` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"] . ': ' . $res["title"];

			$aux["hits"] = $gBitSystem->get_long_datetime($res["comment_date"]);
			$aux["href"] = BITFORUMS_PKG_URL.'view_thread.php?forum_id=' . $res["forum_id"] . '&amp;comments_parent_id=' . $res["thread_id"];
			$ret[] = $aux;
		}

		$ret["data"] = $ret;
		$ret["title"] = tra("Forums last topics");
		$ret["y"] = tra("Topic date");
		return $ret;
	}

	function forums_ranking_most_read_topics($limit) {
		$query = "select
		tc.`hits`,tc.`title`,tf.`name`,tf.`forum_id`,tc.`thread_id`,tc.`object`
		from `".BIT_DB_PREFIX."tiki_comments` tc,`".BIT_DB_PREFIX."tiki_forums` tf where
		`object`=`forum_id` and `object_type` = 'forum' and
		`parent_id`=0 order by tc.`hits` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"] . ': ' . $res["title"];

			$aux["hits"] = $res["hits"];
			$aux["href"] = BITFORUMS_PKG_URL.'view_thread.php?forum_id=' . $res["forum_id"] . '&amp;comments_parent_id=' . $res["thread_id"];
			$ret[] = $aux;
		}

		$ret["data"] = $ret;
		$ret["title"] = tra("Forums most read topics");
		$ret["y"] = tra("Reads");
		return $ret;
	}

	function forums_ranking_top_topics($limit) {
		$query = "select
		tc.`average`,tc.`title`,tf.`name`,tf.`forum_id`,tc.`thread_id`,tc.`object`
		from `".BIT_DB_PREFIX."tiki_comments` tc,`".BIT_DB_PREFIX."tiki_forums` tf where
		`object`=`forum_id` and `object_type` = 'forum' and
		`parent_id`=0 order by tc.`average` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"] . ': ' . $res["title"];

			$aux["hits"] = $res["average"];
			$aux["href"] = BITFORUMS_PKG_URL.'view_thread.php?forum_id=' . $res["forum_id"] . '&amp;comments_parent_id=' . $res["thread_id"];
			$ret[] = $aux;
		}

		$ret["data"] = $ret;
		$ret["title"] = tra("Forums best topics");
		$ret["y"] = tra("Score");
		return $ret;
	}

	function forums_ranking_most_visited_forums($limit) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_forums` order by `hits` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"];

			$aux["hits"] = $res["hits"];
			$aux["href"] = BITFORUMS_PKG_URL.'view.php?forum_id=' . $res["forum_id"];
			$ret[] = $aux;
		}

		$ret["data"] = $ret;
		$ret["title"] = tra("Forums most visited forums");
		$ret["y"] = tra("Visits");
		return $ret;
	}

	function forums_ranking_most_commented_forum($limit) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_forums` order by `comments` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"];

			$aux["hits"] = $res["comments"];
			$aux["href"] = BITFORUMS_PKG_URL.'view.php?forum_id=' . $res["forum_id"];
			$ret[] = $aux;
		}

		$ret["data"] = $ret;
		$ret["title"] = tra("Forums with most posts");
		$ret["y"] = tra("Posts");
		return $ret;
	}

	function gal_ranking_top_galleries($limit) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_galleries` where `visible`=? order by `hits` desc";

		$result = $this->query($query,array('y'),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"];

			$aux["hits"] = $res["hits"];
			$aux["href"] = IMAGEGALS_PKG_URL.'browse.php?gallery_id=' . $res["gallery_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top galleries");
		$retval["y"] = tra("Visits");
		return $retval;
	}

	function filegal_ranking_top_galleries($limit) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_file_galleries` where `visible`=? order by `hits` desc";

		$result = $this->query($query,array('y'),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"];

			$aux["hits"] = $res["hits"];
			$aux["href"] = FILEGALS_PKG_URL.'list_file_gallery.php?gallery_id=' . $res["gallery_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top file galleries");
		$retval["y"] = tra("Visits");
		return $retval;
	}

	function gal_ranking_top_images($limit) {
		$query = "select `image_id`,`name`,`hits` from `".BIT_DB_PREFIX."tiki_images` order by `hits` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"];

			$aux["hits"] = $res["hits"];
			$aux["href"] = IMAGEGALS_PKG_URL.'browse_image.php?image_id=' . $res["image_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top images");
		$retval["y"] = tra("Hits");
		return $retval;
	}

	function filegal_ranking_top_files($limit) {
		$query = "select `file_id`,`filename`,`downloads` from `".BIT_DB_PREFIX."tiki_files` order by `downloads` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["filename"];

			$aux["hits"] = $res["downloads"];
			$aux["href"] = FILEGALS_PKG_URL.'download_file.php?file_id=' . $res["file_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki top files");
		$retval["y"] = tra("Downloads");
		return $retval;
	}

	function gal_ranking_last_images($limit) {
		global $gBitSystem;
		$query = "select `image_id`,`name`,`created` from `".BIT_DB_PREFIX."tiki_images` order by `created` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["name"];

			$aux["hits"] = $gBitSystem->get_long_datetime($res["created"]);
			$aux["href"] = IMAGEGALS_PKG_URL.'browse_image.php?image_id=' . $res["image_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki last images");
		$retval["y"] = tra("Upload date");
		return $retval;
	}

	function filegal_ranking_last_files($limit) {
		global $gBitSystem;
		$query = "select `file_id`,`filename`,`created` from `".BIT_DB_PREFIX."tiki_files` order by `created` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["name"] = $res["filename"];

			$aux["hits"] = $gBitSystem->get_long_datetime($res["created"]);
			$aux["href"] = FILEGALS_PKG_URL.'download_file.php?file_id=' . $res["file_id"];
			$ret[] = $aux;
		}

		$retval["data"] = $ret;
		$retval["title"] = tra("Wiki last files");
		$retval["y"] = tra("Upload date");
		return $retval;
	}

	function cms_ranking_top_articles($limit) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_articles` order by `reads` desc";

		$result = $this->query($query,array(),$limit,0);
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
		$query = "select * from `".BIT_DB_PREFIX."tiki_blogs` order by `hits` desc";

		$result = $this->query($query,array(),$limit,0);
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
		$query = "select * from `".BIT_DB_PREFIX."tiki_blogs` order by `activity` desc";

		$result = $this->query($query,array(),$limit,0);
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
		$query = "select * from `".BIT_DB_PREFIX."tiki_blog_posts` order by `post_id` desc";

		$result = $this->query($query,array(),$limit,0);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$q = "select title, created from `".BIT_DB_PREFIX."tiki_blogs` where `blog_id`=";
			$q.= $res["blog_id"];
			$result2 = $this->query($q,array(),$limit,0);
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
		
		$query = "select distinct users.`login` as `user`, count(*) as `numb` from `".BIT_DB_PREFIX."users_users` users INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`user_id` =  users.`user_id`) where tc.`content_type_guid`='".BITPAGE_CONTENT_TYPE_GUID."' group by `user` order by ".$this->convert_sortmode("numb_desc");

		$result = $this->query($query,array(),$limit,0);
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
		$query = "select distinct `author`, count(*) as `numb` from `".BIT_DB_PREFIX."tiki_articles` group by `author` order by ".$this->convert_sortmode("numb_desc");

		$result = $this->query($query,array(),$limit,0);
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
