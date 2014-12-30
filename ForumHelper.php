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

	public $bb_parser;

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
		return strip_tags(esc_sql($string));
	}

	public static function markSolved($record, $post_id = "")
	{
		global $wpdb;

		$thread = self::getInstance()->getThread($record);
		if ($thread["user_id"] != get_current_user_id()) {
			die("");
		}
		$additional_sql = "";
		if ($post_id) {
			$additional_sql = " ,solved_post_id = '$post_id'";
		}
		$sql = "UPDATE " . AppBase::$threads_table . " SET is_solved = '1' $additional_sql WHERE id = '$record'";
		$result = $wpdb->query($sql);
		return $result;
	}

	public static function deletePost($record)
	{
		if (current_user_can('manage_options')) {
			global $wpdb;
			$sql = "DELETE FROM " . AppBase::$posts_table . "  WHERE id = '$record'";
			$result = $wpdb->query($sql);
			return $result;
		}
		die("");
	}

	public static function deleteThread($record)
	{
		if (current_user_can('manage_options')) {
			global $wpdb;
			$sql = "DELETE FROM " . AppBase::$threads_table . "  WHERE id = '$record'";
			$result = $wpdb->query($sql);

			$sql = "DELETE FROM " . AppBase::$posts_table . " WHERE parent_id = '$record'";
			$result = $wpdb->query($sql);

			return $result;
		}
		die("");
	}

	public static function closeThread($record)
	{
		global $wpdb;
		$thread = self::getInstance()->getThread($record);
		if ($thread["user_id"] != get_current_user_id()) {
			die("");
		}
		$sql = "UPDATE " . AppBase::$threads_table . " SET status = 'closed'  WHERE id = '$record'";
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

		return "<ol class='breadcrumb'><li>" . implode("</li><li>", $result) . "</li></ol>";
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

		return urldecode(get_permalink() . $delim . http_build_query($link_base));
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

	function getThreadsInForumForRSS($forum_id)
	{
		$sql = "SELECT * FROM " . AppBase::$threads_table . " t
			WHERE t.parent_id='$forum_id' order by date limit 4";

		$url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$data = array(
			"site_permalink" => site_url(),
			"site_title" => get_bloginfo("name"),
			"url" => htmlentities($url),
			"description" => get_bloginfo("description"),
			"forum" => $this->getForum($forum_id),
		);
		$data["threads"] = $this->db->get_results($sql, ARRAY_A);
		foreach ($data["threads"] as &$thread) {

			$post = $this->getFirstPost($thread["id"]);

			$thread["text"] = $this->outPutFilter($post["text"]);
			$thread["date"] = date("D, d M Y H:i:s T", strtotime($thread["date"]));;
			$thread["user"] = $this->getUserDataFiltered($thread["user_id"]);
			$thread["avatar"] = $this->getAvatar($thread["user"]->user_email, 22);
			$thread["permalink"] = htmlentities($this->getLink(AppBase::THREAD_VIEW_ACTION, $thread["id"]));
		}

		return $data;
	}

	function getPostsInThreadForRSS($thread_id)
	{
		$sql = "SELECT p.* FROM " . AppBase::$posts_table . " p left join " . AppBase::$threads_table . " t on t.id = p.parent_id WHERE p.parent_id='$thread_id' order by date limit 50";
		$url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$data = array(
			"site_permalink" => site_url(),
			"site_title" => get_bloginfo("name"),
			"url" => htmlentities($url),
			"description" => get_bloginfo("description"),
			"thread" => $this->getThread($thread_id),
		);

		$data["posts"] = $this->db->get_results($sql, ARRAY_A);
		foreach ($data["posts"] as &$post) {
			$post["text"] = $this->outPutFilter($post["text"]);
			$post["date"] = date("D, d M Y H:i:s T", strtotime($post["date"]));;
			$post["user"] = $this->getUserDataFiltered($post["user_id"]);
			$post["avatar"] = $this->getAvatar($post["user"]->user_email, 22);
			$post["permalink"] = htmlentities($this->getLink(AppBase::POST_VIEW_ACTION, $post["id"]));
		}

		return $data;
	}

	/*
	* @param
	* @return
	*/
	public function getPostsInThread($record, $offset)
	{
		$limit_query = "LIMIT $offset," . get_option(AppBase::OPTION_POSTS_VIEW_COUNT);
		$nonce = wp_create_nonce("wpforum_ajax_nonce");
		$sql = "SELECT p.*, t.subject as thread_subject FROM " . AppBase::$posts_table . " p left join " . AppBase::$threads_table . " t on t.id = p.parent_id WHERE p.parent_id='$record' order by date $limit_query";
		$posts["posts"] = $this->db->get_results($sql, ARRAY_A);
		if (!$posts["posts"]) {
			return false;
		}
		$thread = $this->getThread($record);
		foreach ($posts["posts"] as &$post) {
			$post["text"] = $this->outPutFilter($post["text"]);
			$post["user"] = $this->getUserDataFiltered($post["user_id"]);
			$post["avatar"] = $this->getAvatar($post["user"]->user_email, 65);
			$post["post_links"] = array();

			if ((!in_array($thread["status"], array("closed"))) or current_user_can('manage_options')) {
				if (is_user_logged_in()) {
					$post["post_links"]["quote"] = array(
						"link" => "<i class='fa fa-quote-right fa-fw'></i><a href='" . ForumHelper::getLink(AppBase::NEW_POST_VIEW_ACTION, $record, array(AppBase::FORUM_QUOTE, $post["id"])) . "'>&nbsp;Quote</a>",
					);
				}
				if (($thread["user_id"] == get_current_user_id()) or current_user_can('manage_options')) {
					$post["post_links"]["solve_post"] = array(
						"link" => "<i class='fa fa-check fa-fw'></i><a data-nonce='$nonce' data-post-id='" . $post["id"] . "' data-thread-id='$record' class='marksolved' href='javascript:void(0)'>&nbsp;Mark question solved by this post</a>",
					);
				}
				if ((get_current_user_id() == $post["user_id"]) or current_user_can('manage_options')) {
					$post["post_links"]["edit"] = array(
						"link" => "<i class='fa fa-edit fa-fw'></i><a href='" . ForumHelper::getLink(AppBase::EDIT_POST_VIEW_ACTION, $post["id"]) . "'>&nbsp;Edit</a>",
					);
				}
				if (current_user_can('manage_options')) {
					$post["post_links"]["delete"] = array(
						"link" => "<i class='fa fa-remove fa-fw'></i><a data-nonce='$nonce' data-post-id='" . $post["id"] . "' class='deletepost' href='javascript:void(0)'>&nbsp;Delete</a>",
					);
				}
			}
			if (!$thread["is_question"] or $thread["is_solved"]) {
				unset($post["post_links"]["solve_post"]);
			}

		}

		$subject = $thread["subject"];
		$posts["header"] = $subject;
		$posts["prefix"] = $this->getThreadPrefix($thread);
		$posts["thread_starter_id"] = $thread["user_id"];
		$posts["icon"] = self::getPng($thread);
		if (!empty($thread["solved_post_id"])) {
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
		return wpautop(($this->bb_parser->Parse(stripslashes($string))));
	}

	/*
	* @param
	* @return
	*/
	public function getCategories()
	{
		$sql = "SELECT * FROM " . AppBase::$categories_table . " order by sort_order";
		$categories = $this->db->get_results($sql, ARRAY_A);

		if (!$categories) {
			return false;
		}
		foreach ($categories as &$category) {
			$category["forums"] = array();
			foreach ($this->getForumsInCategory($category["id"]) as $forum) {
				$forum["href"] = self::getLink(AppBase::FORUM_VIEW_ACTION, $forum["id"]);
				$category["forums"][$forum["id"]] = $forum;
				$category["forums"][$forum["id"]]["links"]["rss"] = '<span class="pull-right"><a href="' . self::getLink(AppBase::RSS_FORUM_ACTION, $forum["id"]) . '" class="btn btn-link"><i class="fa fa-rss orange"></i>&nbsp;</a></span>';
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
		$sql = "select f.sort_order, f.id, f.name, f.description, max(p.date) as last_post, count(distinct(p.id)) as post_count, count(distinct(t.id)) as thread_count from " . AppBase::$forums_table . " f
					left join " . AppBase::$threads_table . " t on t.parent_id = f.id
						left join " . AppBase::$posts_table . " p on p.parent_id = t.id
						where f.parent_id = '{$category_id}'
				group by f.id order by f.sort_order";
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) {
			return array();
		}

		return $result;
	}

	/*
		* @param
		* @return
		*/
	public function getForums()
	{
		$sql = "select * from " . AppBase::$forums_table . " f
				 order by f.sort_order";
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) {
			return array();
		}

		return $result;
	}

	function getLastPostDate($thread_id)
	{
		$sql = "SELECT max(date) FROM " . AppBase::$posts_table . " WHERE parent_id = '$thread_id'";
		$result = $this->db->get_var($sql);

		return $result;
	}

	/*
	* @param
	* @return
	*/
	public function getThreadsInForum($forum_id, $offset)
	{
		$limit_query = "LIMIT $offset," . get_option(AppBase::OPTION_THREADS_VIEW_COUNT);

		$sql = "select t.*, count(distinct(p.id))-1 as post_replies, max(p.date) as last_post from " . AppBase::$threads_table . " t
			left join " . AppBase::$posts_table . " p on t.id = p.parent_id
				where t.parent_id = '$forum_id'
			group by t.id order by (sticky = '1') DESC, last_post DESC $limit_query";
		$threads = $this->db->get_results($sql, ARRAY_A);

		if (!$threads) {
			return false;
		}
		$lastVisit = "";
		if (is_user_logged_in() and isset($_COOKIE['lastVisit'])) {
			$lastVisit = $_COOKIE['lastVisit'];
		}
		foreach ($threads as &$thread) {

			$nonce = wp_create_nonce("wpforum_ajax_nonce");
			$thread["links"] = array();
			if (current_user_can('manage_options')) {
				$thread["links"]["delete"] = '<span class="pull-right"><button type="button" data-nonce="' . $nonce . '" data-thread-id="' . $thread["id"] . '" class="btn btn-danger btn-xs deletethread"><i class="fa fa-trash"></i> Delete</button></span>';
				$thread["links"]["move"] = '<span class="pull-right"><a href="' . self::getLink(AppBase::MOVE_THREAD_VIEW_ACTION, $thread["id"]) . '" type="button" class="btn btn-primary btn-xs movethread"><i class="fa fa-share"></i> Move</a></span>';
			}
			$thread["href"] = self::getLink(AppBase::THREAD_VIEW_ACTION, $thread["id"]);
			$thread["is_new"] = false;
			if (!empty($lastVisit)) {
				$last_post = $this->getLastPostDate($thread["id"]);
				$thread["is_new"] = ($last_post > $lastVisit) ? 1 : 0;

				$thread["meta"]["last_post_date"] = $last_post;
				$thread["meta"]["lastVisit"] = $lastVisit;
				$thread["links"]["unread"] = '<span class="pull-right"><i class="fa fa-"></i></span>';

			}
			$thread["icon"] = self::getPng($thread);
			$thread["user"] = $this->getUserDataFiltered($thread["user_id"]);
			$thread["last_poster"] = $this->lastPoster($thread["id"]);
			$thread["last_poster"]["avatar"] = $this->getAvatar($thread["last_poster"]["user_email"], 32, "left", "avatar-22");
			$thread["prefix"] = $this->getThreadPrefix($thread);
		}
		return $threads;
	}

	function getAvatar($email, $size, $align = "", $class = "")
	{
		$default = "";
		/* Check if we are using ssl */
		if (is_ssl()) {
			$host = 'https://secure.gravatar.com';
		} else {
			$host = "http://www.gravatar.com";
		}
		$grav_url = "$host/avatar/" . md5(strtolower(trim($email))) . "?d=" . urlencode($default) . "&s=" . $size;

		$avtar_img = "<img align='$align' class='avatar $class' src='$grav_url' height='$size' width='$size'>";

		return $avtar_img;
	}

	function getThreadPrefix(array $thread)
	{
		$prefix = "";

		if ($thread["sticky"] == "1") {
			$prefix .= "<span class='label label-success'>Pinned</span>";
		}
		if ($thread["moved_from"]) {
			$prefix .= "<span class='label label-default'>Moved</span>";
		}
		/*
		if ($thread["status"] == "closed") {
			$prefix .= "<span class='label label-danger'>Closed</span>";
		}
		if ($thread["is_solved"]) {
			$prefix .= "<span class='label label-success'>Solved</span>";
		}
		*/
		return empty($prefix) ? "" : $prefix . " ";
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
		$sql = "select * from " . AppBase::$categories_table . " where id = '{$id}'";
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

	function getFirstPost($thread_id)
	{
		$sql = "select * from " . AppBase::$posts_table . " where parent_id = '{$thread_id}' and nr='1'";
		return $this->db->get_row($sql, ARRAY_A);
	}

	/*
		* @param
		* @return
		*/
	public function getPng($thread)
	{

		if ($thread["status"] == "closed")
			return "closed";

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
					return "open";
				if ($thread["status"] == "open")
					return "open";
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

		unset($user->user_pass);
		unset($user->user_activation_key);
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

	public static function add_category($name, $description, $sort_order)
	{
		global $wpdb;
		$id = create_guid();

		$sql = "INSERT INTO " .
			AppBase::$categories_table . "
				(id, name, description, sort_order)
				VALUES('$id','$name','$description','$sort_order')";

		return $wpdb->query($sql);
	}

	public static function add_forum($name, $description, $sort_order, $category_id)
	{
		global $wpdb;
		$id = create_guid();
		$sql = "INSERT INTO " .
			AppBase::$forums_table . "
				(id, name, description, sort_order, parent_id)
				VALUES('$id','$name','$description','$sort_order','$category_id')";

		return $wpdb->query($sql);
	}


	public static function update_category($id, $name, $description, $sort_order)
	{
		global $wpdb;

		$sql = "UPDATE " . AppBase::$categories_table .
			" SET
			name='$name',
			description='$description',
			sort_order='$sort_order'
			WHERE id='$id'";


		return $wpdb->query($sql);
	}

	public static function update_forum($id, $name, $description, $sort_order, $parent_id)
	{
		global $wpdb;
		$sql = "UPDATE " . AppBase::$forums_table . "
		SET
			name='$name',
			description='$description',
			sort_order='$sort_order',
			parent_id='$parent_id'
			WHERE id='$id'
		";

		return $wpdb->query($sql);
	}

	function getCatDD($selected)
	{
		$cats = $this->getCategories();

		$dd = "<select name='category_id'>";
		$dd .= "<option></option>";

		foreach ($cats as $cat) {
			$s = "";
			if ($cat["id"] == $selected) {
				$s = "selected";
			}

			$dd .= "<option $s value='{$cat["id"]}'>{$cat["name"]}</option>";
		}

		$dd .= "</select>";

		return $dd;
	}

	function getForumDD($selected)
	{
		$cats = $this->getCategories();
		$d = "";
		foreach ($cats as $cat) {
			$d .= "<option disabled>{$cat["name"]}</option>";
			$forums = $this->getForumsInCategory($cat["id"]);

			foreach ($forums as $forum) {
				$s = "";
				if ($forum["id"] == $selected) {
					$s = "selected";
				}
				$d .= "<option $s value='{$forum["id"]}'>&nbsp; -- {$forum["name"]}</option>";
			}
		}

		return $d;
	}
}


?>