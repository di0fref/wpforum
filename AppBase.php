<?php
require_once("ForumHelper.php");
require_once("ForumView.php");
require_once("assets/guid.php");

if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
define('WPFDIR', dirname(plugin_basename(__FILE__)));
define('WPFPATH', WP_CONTENT_DIR . '/plugins/' . WPFDIR . '/');
define('WPFURL', WP_CONTENT_URL . '/plugins/' . WPFDIR . '/');


/*
* Class:
* Author: Fredrik Fahlstad
*/

class AppBase
{
	const CATEGORIES = "wpforum_categories";
	const FORUMS = "wpforum_forums";
	const THREADS = "wpforum_threads";
	const POSTS = "wpforum_posts";
	const USERS = "users";
	const LIKES = "wpforum_likes";
	//const USERS_THREADS = "wpforum_users_threads";

	const FORUM_VIEW_ACTION = "viewforum";
	const THREAD_VIEW_ACTION = "viewthread";
	const NEW_THREAD_VIEW_ACTION = "newthread";
	const NEW_POST_VIEW_ACTION = "newpost";

	const RSS_THREAD_ACTION = "threadrss";
	const RSS_FORUM_ACTION = "forumrss";

	const EMAIL_POST_ACTION = "email_sub";
	const MARK_SOLVED_ACTION = "marksolved";
	const MAIN_VIEW_ACTION = "main";
	const POST_VIEW_ACTION = "viewpost";

	const EDIT_THREAD_VIEW_ACTION = "editthread";
	const EDIT_POST_VIEW_ACTION = "editpost";
	const DELETE_POST_ACTION = "deletepost";
	const DELETE_THREAD_ACTION = "deletethread";
	const MOVE_THREAD_VIEW_ACTION = "movethread";

	const OPTION_DEFAULT_DATE_FORMAT = "%h %e, %Y %l:%S %p";

	const RECORD = "record";
	const APP_ACTION = "action";

	const THREAD_PAGE_COUNT = 5;
	const POST_PAGE_COUNT = 5;
	const FORUM_PAGE = "fpage";
	const FORUM_QUOTE = "quote";
	const FORUM_POST = "fpost";

	const TRAIL_SEPARATOR = " / ";
	const WPFORUM_INSERT_NONCE = "wpforum_insert_nonce";

	/* Options */
	const OPTION_DATE_FORMAT = "wpforum_option_date_format";
	const OPTION_THREADS_VIEW_COUNT = "wpforum_option_threads_view_count";
	const OPTION_POSTS_VIEW_COUNT = "wpforum_option_posts_view_count";

	static $border = 0;

	static $categories_table;
	static $forums_table;
	static $threads_table;
	static $posts_table;
	static $users_table;
	static $likes_table;
	//static $users_threads_table;

	protected $action;
	protected $record;
	protected $page;

	public function __construct()
	{
		global $table_prefix;

		self::$categories_table = $table_prefix . self::CATEGORIES;
		self::$forums_table = $table_prefix . self::FORUMS;
		self::$threads_table = $table_prefix . self::THREADS;
		self::$posts_table = $table_prefix . self::POSTS;
		self::$users_table = $table_prefix . self::USERS;
		self::$likes_table = $table_prefix . self::LIKES;

	}

	public static $defined_actions = array(
		self::FORUM_VIEW_ACTION,
		self::THREAD_VIEW_ACTION,
		self::NEW_THREAD_VIEW_ACTION,
		self::NEW_POST_VIEW_ACTION,
		self::RSS_THREAD_ACTION,
		//self::EMAIL_POST_ACTION,
		self::MARK_SOLVED_ACTION
	);

	function avtivation()
	{
		update_option(self::OPTION_DATE_FORMAT, self::OPTION_DEFAULT_DATE_FORMAT);
		update_option(self::OPTION_THREADS_VIEW_COUNT, 20);
		update_option(self::OPTION_POSTS_VIEW_COUNT, 20);
	}

	public function main($content)
	{
		if (!preg_match('|<!--WPFORUM3-->|', $content))
			return $content;

		$offset = "";

		if (isset($_REQUEST[self::APP_ACTION])) {
			$this->action = $_REQUEST[self::APP_ACTION];
		} else {
			$this->action = self::MAIN_VIEW_ACTION;
		}
		if (isset($_REQUEST[self::RECORD])) {
			$this->record = $_REQUEST[self::RECORD];
			self::checkParams($this->record, "guid");
		}
		if (isset($_REQUEST[self::FORUM_PAGE])) {
			$this->page = $_REQUEST[self::FORUM_PAGE];
			self::checkParams($this->page);
		}
		if (isset($_REQUEST[self::FORUM_POST])) {
			$this->page = $_REQUEST[self::FORUM_POST];
			self::checkParams($this->page, "guid");
		}
		if (isset($_REQUEST[self::FORUM_QUOTE])) {
			self::checkParams($_REQUEST[self::FORUM_QUOTE], "guid");
		}
		$offset = $this->calculateOffset();
		$view = new ForumView($this->action, $this->record, $offset);

		switch ($this->action) {
			case self::FORUM_VIEW_ACTION:
				$data = $view->getForumView();
				break;
			case self::THREAD_VIEW_ACTION:
				$data = $view->getTopicView();
				break;
			case self::NEW_THREAD_VIEW_ACTION:
				if (is_user_logged_in()) {
					$data = $view->getNewThreadView();
				} else {
					$data = $view->permission(self::NEW_THREAD_VIEW_ACTION);
				}
				break;
			case self::NEW_POST_VIEW_ACTION:
				if (is_user_logged_in()) {
					$data = $view->getNewPostView();
				} else {
					$data = $view->permission(self::NEW_POST_VIEW_ACTION);
				}
				break;
			case self::EDIT_POST_VIEW_ACTION:
				if (is_user_logged_in()) {
					$data = $view->getEditPostView();
				} else {
					$data = $view->permission(self::NEW_POST_VIEW_ACTION);
				}
				break;
			case self::EDIT_THREAD_VIEW_ACTION:
				if (is_user_logged_in()) {
					$data = $view->getEditThreadView();
				} else {
					$data = $view->permission(self::EDIT_THREAD_VIEW_ACTION);
				}
				break;
			case self::MOVE_THREAD_VIEW_ACTION:
				if (is_user_logged_in()) {
					$data = $view->getMoveThreadView();
				} else {
					$data = $view->permission(self::MOVE_THREAD_VIEW_ACTION);
				}
				break;
			case self::MAIN_VIEW_ACTION:
				$data = $view->getMainView();
				break;
			default:
				wp_die("Error!");
		}

		$header = $this->getHeader();
		$footer = $this->getFooter();

		$out = "<div id='forum-wrapper'>" . $header . $data . $footer . "</div>";

		return preg_replace('|<!--WPFORUM3-->|', $out, $content);

	}

	/*
	* @param
	* @return
	*/
	public function calculateOffset()
	{
		switch ($this->action) {
			case AppBase::FORUM_VIEW_ACTION:
				$count = AppBase::THREAD_PAGE_COUNT;
				break;
			case AppBase::THREAD_VIEW_ACTION:
				$count = AppBase::POST_PAGE_COUNT;
				break;
			default:
				$count = 0;
		}
		if ($this->page == 1 or empty($this->page)) {
			$start = 0;
		} else {
			$start = ($this->page - 1) * $count;
		}
		return $start;
	}

	public function getHeader()
	{
	}

	public function getFooter()
	{
		$out = "";
		if (!empty($this->action)) {
			$out .= '<div style="text-align:right"><ul class="pagination pagination-sm">';

			$out .= paginate(get_permalink() . "?" . AppBase::APP_ACTION . "=" . $this->action . "&record={$this->record}", $this->page, ForumHelper::getTotalPages($this->action, $this->record));
			$out .= "</ul></div>";
		}
		$out .= '<div id="forum-dialog" title="Dialog">';
		return $out;
	}

	public static function checkParams($parm, $type = "")
	{
		switch ($type) {
			case "guid":
				if (!is_guid($parm) and !is_numeric($parm)) {
					wp_die("Input error, please try again.");
				}
				return true;
				break;
		}

		$regexp = "/^([+-]?((([0-9]+(\.)?)|([0-9]*\.[0-9]+))([eE][+-]?[0-9]+)?))$/";
		if (!preg_match($regexp, $parm))
			wp_die("Input error, please try again.");
	}


	/*
	* @param
	* @return
	*/
	public function install()
	{

		$categories_sql = "
			CREATE TABLE IF NOT EXISTS " . self::$categories_table . " (
			  id varchar(36) NOT NULL default '',
			  `name` varchar(255) NOT NULL default '',
			  `description` varchar(255) default '',
			  sort_order int(11) default 0,
			  PRIMARY KEY  (id)
			);";

		$forums_sql = "
			CREATE TABLE IF NOT EXISTS " . self::$forums_table . " (
			  id varchar(36) NOT NULL default '',
			  `name` varchar(255) NOT NULL default '',
			  parent_id varchar(36) NOT NULL default '',
			  description varchar(255) NOT NULL default '',
			  sort_order int(11) default 0,
			  PRIMARY KEY  (id),
			  INDEX parent_idx (parent_id)
			);";

		$threads_sql = "
			CREATE TABLE IF NOT EXISTS " . self::$threads_table . " (
			  id varchar(36) NOT NULL default '',
			  parent_id varchar(36) NOT NULL default '',
			  views int(11) NOT NULL default '0',
			  `subject` varchar(255) NOT NULL default '',
			  `date` datetime NOT NULL default '0000-00-00 00:00:00',
			  `status` varchar(20) NOT NULL default 'open',
			  is_question bool default 0,
			  is_solved bool default 0,
			  solved_post_id varchar(36)  default '',
			  user_id int(11) NOT NULL,
			  sticky bool default 0,
			  moved_from varchar(36) default '',
			  PRIMARY KEY  (id),
			  INDEX parent_idx (parent_id),
			  INDEX user_idx (user_id)
			);";

		$posts_sql = "
			CREATE TABLE IF NOT EXISTS " . self::$posts_table . " (
			  id varchar(36) NOT NULL default '',
			  `text` longtext,
			  parent_id varchar(36) NOT NULL default '',
			  `date` datetime NOT NULL default '0000-00-00 00:00:00',
			  user_id int(11) NOT NULL default '0',
			  `subject` varchar(255) NOT NULL default '',
			  nr int(11) NOT NULL,
			  PRIMARY KEY  (id),
			  INDEX parent_idx (parent_id),
			  INDEX user_idx (user_id)
			);";
		/*
				$likes_sql = "
					CREATE TABLE IF NOT EXISTS " . self::$likes_table . " (
					  id varchar(36) NOT NULL default '',
					  user_id varchar(36) NOT NULL default '',
					  thread_id varchar(36) NOT NULL default '',
					  PRIMARY KEY  (id),
					  INDEX thread_idx (thread_id),
					  INDEX user_idx (user_id)
					);";
		*/
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

		dbDelta($categories_sql);
		dbDelta($forums_sql);
		dbDelta($threads_sql);
		dbDelta($posts_sql);
//		dbDelta($likes_sql);

	}

	/*
	* @param
	* @return
	*/
	public function head()
	{

	}

	/*
	* @param
	* @return
	*/
	public function enqueue_scripts()
	{
		wp_register_style('wpforum_styles', plugins_url('assets/styles/style.css', __FILE__), array(), '', 'all');
		wp_register_style('jquery_ui_styles', plugins_url('assets/js/jquery-ui/jquery-ui.min.css', __FILE__), array(), '', 'all');
		wp_register_style('wpforum_bootstrap_styles', plugins_url('assets/bootstrap-3.3.1/css/bootstrap.min.css', __FILE__), array(), '3.3.1', 'all');
		wp_register_style('wpforum_bootstrap_styles_theme', plugins_url('assets/bootstrap-3.3.1/css/bootstrap-theme.min.css', __FILE__), array(), '3.3.1', 'all');
		wp_register_style('wpforum_font_awsome', plugins_url('assets/font-awesome/css/font-awesome.min.css', __FILE__), array(), '', 'all');

		wp_register_style('wysibb_css',plugins_url("assets/wysibb/theme/default/wbbtheme.css", __FILE__), array(), '', 'all');
		wp_enqueue_style('wysibb_css');


		wp_enqueue_style('wpforum_styles');
		wp_enqueue_style('wpforum_bootstrap_styles');
		wp_enqueue_style('wpforum_bootstrap_styles_theme');
		wp_enqueue_style('jquery_ui_styles');
		wp_enqueue_style('wpforum_font_awsome');

		wp_register_script('jquery_ui', plugins_url('assets/js/jquery-ui/jquery-ui.min.js', __FILE__), array("jquery"), '', false);
		wp_register_script('wpforum_script', plugins_url('assets/js/forum.js', __FILE__), array("jquery"), '', false);
		wp_register_script('jquery_validate', plugins_url('assets/js/jquery.validate.min.js', __FILE__), array("jquery"), '', false);
		wp_register_script('bootstrap', plugins_url('assets/bootstrap-3.3.1/js/bootstrap.min.js', __FILE__), array("jquery"), '3.3.1', false);
		wp_register_script('jquery_confirm', plugins_url('assets/js/jquery.confirm/jquery.confirm.min.js', __FILE__), array("jquery"), '', false);

		wp_register_script("wysibb", plugins_url("assets/wysibb/jquery.wysibb.min.js", __FILE__), array("jquery"), "", false);

		wp_enqueue_script('wpforum_script');
		wp_enqueue_script('jquery_ui');
		wp_enqueue_script('jquery_validate');
		wp_enqueue_script('bootstrap');

		wp_enqueue_script('wysibb');

		wp_enqueue_script('jquery_confirm');

		wp_localize_script('wpforum_script', 'forumAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

	}

	function preHeader()
	{
		/* RSS Feed hook*/
		if(isset($_REQUEST[self::APP_ACTION]) and $_REQUEST[self::APP_ACTION] == self::RSS_THREAD_ACTION){
			header('Content-Type: text/xml; charset=UTF-8');
			die(ForumView::getThreadRSS());
		}
		/* RSS Feed hook*/
		if(isset($_REQUEST[self::APP_ACTION]) and $_REQUEST[self::APP_ACTION] == self::RSS_FORUM_ACTION){
			header('Content-Type: text/xml; charset=UTF-8');
			die(ForumView::getForumRSS());
		}
		/* Processing forms */

		/* New thread */
		if (isset($_POST["forum-form-move-thread"])) {
			if (!current_user_can('manage_options')) {
				wp_die("No naughty business please");
			}
			self::verifyNonce(self::WPFORUM_INSERT_NONCE);
			include("MoveThread.php");
			header("Location:" . $redirect_url);
			exit();
		}

		/* New thread */
		if (isset($_POST["forum-form-new-thread"])) {
			if (!is_user_logged_in()) {
				wp_die("No naughty business please");
			}
			self::verifyNonce(self::WPFORUM_INSERT_NONCE);
			include("AddThread.php");
			header("Location:" . $redirect_url);
			exit();
		}
		/* Edit thread */
		if (isset($_POST["forum-form-edit-thread"])) {
			if (!is_user_logged_in()) {
				wp_die("No naughty business please");
			}
			self::verifyNonce(self::WPFORUM_INSERT_NONCE);
			include("EditThread.php");
			header("Location:" . $redirect_url);
			exit();
		}
		/* Post reply*/
		if (isset($_POST["forum-form-new-post"])) {
			if (!is_user_logged_in()) {
				wp_die("No naughty business please");
			}
			self::verifyNonce(self::WPFORUM_INSERT_NONCE);
			include("AddPost.php");

			$this->notifyThreadStarter($thread_id, $post_id);

			header("Location:" . $redirect_url);
			exit();
		}

		/* Edit reply*/
		if (isset($_POST["forum-form-edit-post"])) {
			if (!is_user_logged_in()) {
				wp_die("No naughty business please");
			}
			self::verifyNonce(self::WPFORUM_INSERT_NONCE);
			include("EditPost.php");
			header("Location:" . $redirect_url);
			exit();
		}
	}

	public static function verifyNonce($nonce)
	{
		if (!wp_verify_nonce($_REQUEST['nonce'], $nonce)) {
			wp_die("No naughty business please");
		}
	}

	function notifyThreadStarter($thread_id, $post_id)
	{
		$user = ForumHelper::getInstance()->getUserDataFiltered(get_current_user_id());

		$thread = ForumHelper::getInstance()->getThread($thread_id);
		$post = ForumHelper::getInstance()->getPost($post_id);

		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

		$subject = get_bloginfo("name") . " - Forum - New Reply";
		$body = "There are a new reply on your topic <a href=''>{$thread["subject"]}</a>\n\n>";
		$body .= "By: {$user->display_name}<br>";
		$body .= "Date: " . strftime(get_option(AppBase::OPTION_DATE_FORMAT), strtotime($post["date"])) . "<br><br>";
		$body .= "Reply:<br>";
		$body .= ForumHelper::getInstance()->bb_parser->Parse("{$post["text"]}");

		wp_mail($user->user_email, $subject, $body, $headers);
	}
}


function _paginate($reload, $page, $tpages)
{
	$data = array();
	$delim = "&";
	if ($tpages > 1) {

		if (empty($page)) $page = 1;

		$adjacents = 4;
		$prevlabel = "&lsaquo; Prev";
		$nextlabel = "Next &rsaquo;";
		$out = "";
		// previous
		if ($page == 1) {
			//$data[] = "<span style='white-space:nowrap'>" . $prevlabel . "</span>\n";
			$data[] = $prevlabel;
		} elseif ($page == 2) {
			//$data[] = "<li style='white-space:nowrap'><a  href=\"" . $reload . "\">" . $prevlabel . "</a>\n</li>";
			$data[] = "<a  href=\"" . $reload . "\">" . $prevlabel . "</a>";
		} else {
			//$data[] = "<li style='white-space:nowrap'><a  href=\"" . $reload . "$delim".AppBase::FORUM_PAGE."=" . ($page - 1) . "\">" . $prevlabel . "</a>\n</li>";
			$data[] = "<a  href=\"" . $reload . "$delim" . AppBase::FORUM_PAGE . "=" . ($page - 1) . "\">" . $prevlabel . "</a>";
		}
		$pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
		$pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;
		for ($i = $pmin; $i <= $pmax; $i++) {
			if ($i == $page) {
				//$data[] = "<li  class=\"active\"><a href=''>" . $i . "</a></li>\n";
				$data[] = "<li  class=\"active\"><a class='active' href=''>" . $i . "</a></li>\n";
			} elseif ($i == 1) {
				//$data[] = "<li><a  href=\"" . $reload . "\">" . $i . "</a>\n</li>";
				$data[] = "<a  href=\"" . $reload . "\">" . $i . "</a>";
			} else {
				//$data[] = "<li><a  href=\"" . $reload . "$delim".AppBase::FORUM_PAGE."=" . $i . "\">" . $i . "</a>\n</li>";
				$data[] = "<a  href=\"" . $reload . "$delim" . AppBase::FORUM_PAGE . "=" . $i . "\">" . $i . "</a>";
			}
		}

		if ($page < ($tpages - $adjacents)) {
			//$data[] = "<a style='font-size:11px' href=\"" . $reload . "$delim".AppBase::FORUM_PAGE."=" . $tpages . "\">" . $tpages . "</a>\n";
			$data[] = "<a style='font-size:11px' href=\"" . $reload . "$delim" . AppBase::FORUM_PAGE . "=" . $tpages . "\">" . $tpages . "</a>\n";

		}
		// next
		if ($page < $tpages) {
			//$data[] = "<li style='white-space:nowrap'><a  href=\"" . $reload . "$delim".AppBase::FORUM_PAGE."=" . ($page + 1) . "\">" . $nextlabel . "</a>\n</li>";
			$data[] = "<a  href=\"" . $reload . "$delim" . AppBase::FORUM_PAGE . "=" . ($page + 1) . "\">" . $nextlabel . "</a>";

		} else {
			//$data[] = "<span style='font-size:11px white-space:nowrap'>" . $nextlabel . "</span>\n";
			$data[] = "<span style='font-size:11px white-space:nowrap'>" . $nextlabel . "</span>\n";
		}
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		//$out .= "";
		return $out;
	}

}

function paginate($reload, $page, $tpages)
{
	$delim = "&";
	if ($tpages > 1) {

		if (empty($page)) $page = 1;

		$adjacents = 4;
		$prevlabel = "&lsaquo; Prev";
		$nextlabel = "Next &rsaquo;";
		$out = "";
		// previous
		if ($page == 1) {
			$out .= "<li><a href='#'>$prevlabel</a></li>";
		} elseif ($page == 2) {
			$out .= "<li><a  href=\"" . $reload . "\">" . $prevlabel . "</a>\n</li>";
		} else {
			$out .= "<li><a  href=\"" . $reload . "$delim" . AppBase::FORUM_PAGE . "=" . ($page - 1) . "\">" . $prevlabel . "</a>\n</li>";
		}

		$pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
		$pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;
		for ($i = $pmin; $i <= $pmax; $i++) {
			if ($i == $page) {
				$out .= "<li  class=\"active\"><a href=''>" . $i . "</a></li>\n";
			} elseif ($i == 1) {
				$out .= "<li><a href=\"" . $reload . "\">" . $i . "</a></li>";
			} else {
				$out .= "<li><a href=\"" . $reload . "$delim" . AppBase::FORUM_PAGE . "=" . $i . "\">" . $i . "</a>\n</li>";
			}
		}

		if ($page < ($tpages - $adjacents)) {
			$out .= "<a style='font-size:11px' href=\"" . $reload . "$delim" . AppBase::FORUM_PAGE . "=" . $tpages . "\">" . $tpages . "</a>\n";
		}
		// next
		if ($page < $tpages) {
			$out .= "<li><a  href=\"" . $reload . "$delim" . AppBase::FORUM_PAGE . "=" . ($page + 1) . "\">" . $nextlabel . "</a>\n</li>";
		} else {
			$out .= "<li><a href='#'>$nextlabel</a></li>";
		}
		$out .= "";
		return $out;
	}

}
