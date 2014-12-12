<?php

require_once(ABSPATH . '/wp-load.php');
//require_once("assets/bbcode.php");
require_once("assets/nbbc/nbbc.php");

/*
* Class:
* Author: Fredrik Fahlstad
*/

class ForumHelper
{
	public $db;
	protected static $_instance;

	protected $bb_parser;

	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct()
	{
		global $wpdb;
		$this->db = $wpdb;
		$this->bb_parser = new BBCode();
		$this->bb_parser->SetSmileyURL(plugins_url("assets/nbbc/smileys", __FILE__));
	}

	public static function input_filter($string)
	{
		global $wpdb;
		return strip_tags($wpdb->escape($string));
	}

	public static function markSolved($record, $post_id = "")
	{
		global $wpdb;
		$additional_sql = "";
		if($post_id){
			$additional_sql = " ,solved_post_id = '$post_id'";
		}
		$sql = "UPDATE " . AppBase::$threads_table . " SET is_solved = '1' $additional_sql WHERE id = '$record'";
		$result = $wpdb->query($sql);
		return $result;
	}

	/*
	* @param
	* @return
	*/
	public static function getTotalPages($action, $record)
	{
		global $wpdb;
		switch ($action) {
			case AppBase::FORUM_VIEW_ACTION:
				$per_page = AppBase::THREAD_PAGE_COUNT;
				$table = AppBase::$threads_table;
				break;
			case AppBase::THREAD_VIEW_ACTION:
				$per_page = AppBase::POST_PAGE_COUNT;
				$table = AppBase::$posts_table;
				break;
			default:
				return 1;
		}
		$sql = "SELECT count(*) FROM $table where parent_id = '$record'";
		$total_results = $wpdb->get_var($sql);
		$total_pages = ceil($total_results / $per_page);

		return $total_pages;
	}

	public function getTrail($action, $record)
	{
		/* Page > Forum -> Topic */
		$link_base = "<a href='%s'>%s</a>";

		$result = array(
			get_the_title()
		);

		switch ($action) {
			case AppBase::FORUM_VIEW_ACTION:
				/* BASE -> CATEGORY -> FORUM */
				$forum = $this->getForum($record);
				$category = $this->getCategory($forum["parent_id"]);
				$result[] = sprintf($link_base, get_permalink(), $category["name"]);
				$result[] = $forum["name"];
				break;
			case AppBase::THREAD_VIEW_ACTION:
				/* BASE -> CATEGORY -> FORUM -> THREAD */
				$thread = $this->getThread($record);
				$forum = $this->getForum($thread["parent_id"]);
				$category = $this->getCategory($forum["parent_id"]);
				$result[] = sprintf($link_base, get_permalink(), $category["name"]);
				$result[] = sprintf($link_base, self::getLink(AppBase::FORUM_VIEW_ACTION, $forum["id"]), $forum["name"]);
				$result[] = $thread["subject"];
				break;
			case AppBase::NEW_THREAD_VIEW_ACTION:
				/* BASE -> CATEGORY -> FORUM -> New Thread*/
				$forum = $this->getForum($record);
				$category = $this->getCategory($forum["parent_id"]);
				$result[] = sprintf($link_base, get_permalink(), $category["name"]);
				$result[] = $forum["name"];
				break;
			case AppBase::NEW_POST_VIEW_ACTION:
				/* BASE -> CATEGORY -> FORUM -> THREAD */
				$thread = $this->getThread($record);
				$forum = $this->getForum($thread["parent_id"]);
				$category = $this->getCategory($forum["parent_id"]);
				$result[] = sprintf($link_base, get_permalink(), $category["name"]);
				$result[] = sprintf($link_base, self::getLink(AppBase::FORUM_VIEW_ACTION, $forum["id"]), $forum["name"]);
				$result[] = $thread["subject"];
				break;
			default:
				break;
		}

		return implode(AppBase::TRAIL_SEPARATOR, $result);
	}

	/*
	* @param
	* @return
	*/
	public static function getLink($action, $record, $additional_params = "")
	{
		global $wp_rewrite;
		$delim = ($wp_rewrite->using_permalinks()) ? "?" : "&";

		$link_base = array(
			AppBase::APP_ACTION => $action,
			AppBase::RECORD => $record,
		);

		if (is_array($additional_params)) {
			$link_base[$additional_params[0]] = $additional_params[1];
		}

		return get_permalink() . $delim . http_build_query($link_base);
	}

	/*
	* @param
	* @return
	*/
	public function updateThreadViewCount($thread_id)
	{
		$thread = $this->getThread($thread_id);

		if ($thread["user_id"] != get_current_user_id()) {
			$sql = "update " . AppBase::$threads_table . " set views = views+1 where id ='$thread_id'";
			$this->db->query($sql);
		}
	}

	/*
	* @param
	* @return
	*/
	public function getPostsInThread($record, $offset)
	{
		$limit_query = "LIMIT $offset," . AppBase::POST_PAGE_COUNT;
		$nonce = wp_create_nonce("wpforum_ajax_nonce");
		$sql = "SELECT p.*, t.subject as thread_subject FROM " . AppBase::$posts_table . " p left join " . AppBase::$threads_table . " t on t.id = p.parent_id WHERE p.parent_id='$record' order by date $limit_query";
		$posts["posts"] = $this->db->get_results($sql, ARRAY_A);
		if (!$posts["posts"]) {
			return false;
		}
		$thread = $this->getThread($record);
		foreach ($posts["posts"] as &$post) {
			$post["text"] = $this->outPutFilter($post["text"]);
			$post["avatar"] = get_avatar($post["user_id"], 65);
			$post["user"] = $this->getUserDataFiltered($post["user_id"]);

			if (!in_array($thread["status"], array("closed")) and $thread["user_id"] == get_current_user_id()) {
				$post["post_links"] = array(
					"edit" => array(
						"href" => "#",
						"text" => "Edit",
					),
					"solve_post" => array(
						"href" => "<a data-nonce='$nonce' data-post-id='".$post["id"]."' data-thread-id='$record' class='marksolved' href='javascript:void(0)'>Mark question solved by this post</a>",
					),
					"quote" => array(
						"href" => ForumHelper::getLink(AppBase::NEW_POST_VIEW_ACTION, $record, array(AppBase::FORUM_QUOTE, $post["id"])),
						"text" => "Reply With Quote",
					),
				);
				if(!$thread["is_question"] or $thread["is_solved"]){
					unset($post["post_links"]["solve_post"]);
				}
			}
		}

		$subject = $thread["subject"];
		$posts["header"] = $subject;
		$posts["prefix"] = $this->getThreadPrefix($thread);
		$posts["thread_starter_id"] = $thread["user_id"];
		if(!empty($thread["solved_post_id"])){
			$solved_post = $this->getPost($thread["solved_post_id"]);
			$solved_user = $this->getUserDataFiltered($solved_post["user_id"]);
			$posts["solved_post_id"] = $thread["solved_post_id"];
			$posts["solved_text"] = $this->outPutFilter($solved_post["text"]);
			$posts["solved_title"] = "This solved my question";
			$posts["solved_user"] = $solved_user;
			$posts["solved_date"] = $solved_post["date"];

		}

		return $posts;
	}

	function outPutFilter($string)
	{
		return stripslashes($this->bb_parser->Parse($string));
	}

	/*
	* @param
	* @return
	*/
	public function getCategories()
	{
		$sql = "SELECT * FROM " . AppBase::$categories_table . " order by name";
		$categories = $this->db->get_results($sql, ARRAY_A);

		if (!$categories) {
			return false;
		}
		foreach ($categories as &$category) {
			foreach ($this->getForumsInCategory($category["id"]) as $forum) {
				$forum["href"] = self::getLink(AppBase::FORUM_VIEW_ACTION, $forum["id"]);
				$category["forums"][] = $forum;
			}
		}

		return $categories;
	}

	/*
	* @param
	* @return
	*/
	public function getForumsInCategory($category_id)
	{
		$sql = "select f.id, f.name, f.description, max(p.date) as last_post, count(distinct(p.id)) as post_count, count(distinct(t.id)) as thread_count from " . AppBase::$forums_table . " f
					left join " . AppBase::$threads_table . " t on t.parent_id = f.id
						left join " . AppBase::$posts_table . " p on p.parent_id = t.id
						where f.parent_id = '{$category_id}'
				group by f.id;";
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) {
			return false;
		}
		return $result;
	}

	/*
	* @param
	* @return
	*/
	public function getThreadsInForum($forum_id, $offset)
	{
		$limit_query = "LIMIT $offset," . AppBase::THREAD_PAGE_COUNT;

		$sql = "select t.*, count(distinct(p.id))-1 as post_replies, max(p.date) as last_post from " . AppBase::$threads_table . " t
			left join " . AppBase::$posts_table . " p on t.id = p.parent_id
				where t.parent_id = '$forum_id'
			group by t.id order by (status = 'sticky') DESC, last_post DESC $limit_query ";
		$threads = $this->db->get_results($sql, ARRAY_A);
		if (!$threads) {
			return false;
		}

		foreach ($threads as &$thread) {
			$thread["href"] = self::getLink(AppBase::THREAD_VIEW_ACTION, $thread["id"]);
			$thread["icon"] = self::getPng($thread);
			$thread["user"] = $this->getUserDataFiltered($thread["user_id"]);
			$thread["last_poster"] = $this->lastPoster($thread["id"]);
			$thread["last_poster"]["avatar"] = get_avatar($thread["last_poster"]["user_email"], 22);
			$thread["prefix"] = $this->getThreadPrefix($thread);
		}
		return $threads;
	}

	function getThreadPrefix(array $thread)
	{
		$prefix = "";
		if ($thread["is_solved"]) {
			$prefix = "<span class='forum-solved-prefix'>[Solved]</span> ";
		}
		if ($thread["status"] == "sticky") {
			$prefix = "<span class='forum-sticky-prefix'>[Sticky]</span> ";
		}
		if ($thread["status"] == "closed") {
			$prefix = "<span class='forum-closed-prefix'>[Closed]</span> ";
		}
		return $prefix;
	}


	public function getPostText($id)
	{
		$sql = "select text from " . AppBase::$posts_table . " where id = '{$id}'";
		return $this->db->get_row($sql, ARRAY_A);
	}

	public function lastPoster($thread_id)
	{
		$sql = "select u.display_name, u.ID, u.user_email from " . AppBase::$users_table . " u LEFT JOIN  " . AppBase::$posts_table . " p on u.id = p.user_id where parent_id = '{$thread_id}' order by date DESC limit 1";
		return $this->db->get_row($sql, ARRAY_A);
	}

	public function getCategory($id)
	{
		$sql = "select name, id from " . AppBase::$categories_table . " where id = '{$id}'";
		return $this->db->get_row($sql, ARRAY_A);
	}

	public function getForum($id)
	{
		$sql = "select * from " . AppBase::$forums_table . " where id = '{$id}'";
		return $this->db->get_row($sql, ARRAY_A);
	}

	public function getThread($id)
	{
		$sql = "select * from " . AppBase::$threads_table . " where id = '{$id}'";
		return $this->db->get_row($sql, ARRAY_A);
	}

	public function getPost($id)
	{
		$sql = "select * from " . AppBase::$posts_table . " where id = '{$id}'";
		return $this->db->get_row($sql, ARRAY_A);
	}

	/*
		* @param
		* @return
		*/
	public function getPng($thread)
	{
		switch ($thread["is_question"]) {
			case "1":
				if ($thread["is_solved"]) {
					return "solved";
				} else {
					return "question";
				}
				break;
			case "0":
				if ($thread["status"] == "sticky")
					return "sticky";
				if ($thread["status"] == "open")
					return "open";
				if ($thread["status"] == "closed")
					return "closed";
				break;
			default:
				return "open";
		}
	}

	/*
	* @param
	* @return
	*/
	public function getIcon($thread)
	{
		switch ($thread["is_question"]) {
			case "1":
				if ($thread["is_solved"]) {
					return "thread-solved";
				} else {
					return "thread-is-question";
				}
				break;
			case "0":
				if ($thread["status"] == "sticky")
					return "thread-sticky";
				if ($thread["status"] == "open")
					return "thread-open";
				if ($thread["status"] == "closed")
					return "thread-closed";
				break;
			default:
				return "thread-open";
		}
	}

	function getUserDataFiltered($user_id)
	{
		static $user_post_count;

		$metas = array(
			"description",
		);
		$user = get_userdata($user_id)->data;
		foreach ($metas as $meta) {
			$user->meta[$meta] = get_user_meta($user_id, $meta, true);
		}

		if (!is_array($user_post_count) or !array_key_exists($user_id, $user_post_count)) {
			$user->post_count = $this->getUserPostCount($user_id);
			$user_post_count[$user_id] = $user->post_count;
		} else {
			$user->post_count = $user_post_count[$user_id];
		}
		return $user;
	}

	function getUserPostCount($user_id)
	{
		$sql = "SELECT count(*) from " . AppBase::$posts_table . " WHERE user_id = '$user_id'";
		return $this->db->get_var($sql);
	}

	static function getNextPostNr($thread_id)
	{
		global $wpdb;
		$sql = "SELECT max(nr) from " . AppBase::$posts_table . " WHERE parent_id = '$thread_id'";
		return $wpdb->get_var($sql) + 1;

	}
}


?>