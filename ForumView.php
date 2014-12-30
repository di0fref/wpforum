<?php
require_once("assets/Smarty/libs/Smarty.class.php");
require_once("ForumHelper.php");

/*
* Class:
* Author: Fredrik Fahlstad
*/

class ForumView
{
	protected $smarty;
	protected $template_dir;
	protected $helper;
	protected $action;
	protected $record;
	protected $offset;

	public function __construct($action, $record, $offset)
	{
		$this->action = $action;
		$this->record = $record;
		$this->offset = $offset;

		$this->template_dir = WPFPATH . "/tpls";
		$this->smarty = new Smarty();
		$this->helper = new ForumHelper();
		$this->assignMisc();
		$this->assignButtons();
		$this->assignTrail();
	}

	function permission($action)
	{
		switch ($action) {
			case AppBase::NEW_THREAD_VIEW_ACTION:
				$message = "You need to be logged in to start a new topic.";
				break;
			case AppBase::EDIT_THREAD_VIEW_ACTION:
				$message = "You do not have permission edit this topic.";
				break;
			case AppBase::NEW_POST_VIEW_ACTION:
				$message = "You need to be logged in to reply to this topic.";
				break;
			case AppBase::EDIT_POST_VIEW_ACTION:
				$message = "You do not have permission edit this post.";
				break;
			default:
				$message = "You do not have permission to view this page.";
				break;
		}
		$this->smarty->assign("message", $message);
		return $this->smarty->fetch($this->template_dir . "/permission.tpl");
	}

	function getNewThreadView()
	{
		$forum = $this->helper->getForum($_REQUEST["record"]);
		if (!$forum) {
			self::_exit();
		}
		$this->smarty->assign("record", $_REQUEST["record"]);
		$this->smarty->assign("nonce", wp_create_nonce("wpforum_insert_nonce"));
		return $this->smarty->fetch($this->template_dir . "/new_thread_form.tpl");
	}

	function getEditPostView()
	{
		$post = $this->helper->getPost($_REQUEST["record"]);

		$post["text"] = stripslashes($post["text"]);
		$post["subject"] = stripslashes($post["subject"]);

		$this->smarty->assign("nonce", wp_create_nonce("wpforum_insert_nonce"));
		$this->smarty->assign("post", $post);
		return $this->smarty->fetch($this->template_dir . "/edit_post_form.tpl");
	}

	function getEditThreadView()
	{
		$thread = $this->helper->getThread($_REQUEST["record"]);
		$thread["subject"] = stripslashes($thread["subject"]);
		$this->smarty->assign("user_can_pin", 0);
		if (current_user_can('manage_options')) {
			$this->smarty->assign("user_can_pin", 1);
		}
		$this->smarty->assign("nonce", wp_create_nonce("wpforum_insert_nonce"));
		$this->smarty->assign("thread", $thread);
		return $this->smarty->fetch($this->template_dir . "/edit_thread_form.tpl");
	}

	function getNewPostView()
	{
		/* Make sure the thread exist */
		$thread = $this->helper->getThread($_REQUEST["record"]);
		if (!$thread) {
			self::_exit();
		}
		if (isset($_REQUEST[AppBase::FORUM_QUOTE])) {
			$post = $this->helper->getPost($_REQUEST[AppBase::FORUM_QUOTE]);
			$user = $this->helper->getUserDataFiltered($post["user_id"]);

			$quote_data = array(
				"text" => stripslashes('[quote name=' . $user->display_name . ' date="' . strftime(get_option(AppBase::OPTION_DATE_FORMAT)) . '"]' . $post["text"] . '[/quote]'),
				"subject" => stripslashes("Re: " . $thread["subject"]),
				"permalink" => "action=viewpost&record={$post["id"]}",
			);

			$this->smarty->assign("quote_data", $quote_data);
		}

		$this->smarty->assign("record", $_REQUEST["record"]);
		$this->smarty->assign("thread_name", $thread["subject"]);
		$this->smarty->assign("nonce", wp_create_nonce("wpforum_insert_nonce"));
		return $this->smarty->fetch($this->template_dir . "/new_post_form.tpl");
	}

	function getMoveThreadView()
	{
		$thread = ForumHelper::getInstance()->getThread($this->record);
		$forum = ForumHelper::getInstance()->getForum($thread["parent_id"]);

		$this->smarty->assign("nonce", wp_create_nonce("wpforum_insert_nonce"));
		$this->smarty->assign("thread", $thread);
		$this->smarty->assign("forum", $forum);
		$this->smarty->assign("forumDD", $this->helper->getForumDD($forum["id"]));

		return $this->smarty->fetch($this->template_dir . "/move_thread_form.tpl");
	}

	function assignTrail()
	{
		$this->smarty->assign("trail", $this->helper->getTrail($this->action, $this->record));
	}

	public function assignButtons()
	{
		$current_user_id = get_current_user_id();

		$links = array();

		if (is_user_logged_in()) {
			$nonce = wp_create_nonce("wpforum_ajax_nonce");

			$links[AppBase::MAIN_VIEW_ACTION] = "";
			$links[AppBase::NEW_THREAD_VIEW_ACTION] = "";
			$links[AppBase::NEW_POST_VIEW_ACTION] = "";
			$links[AppBase::EDIT_POST_VIEW_ACTION] = "";
			$links[AppBase::EDIT_THREAD_VIEW_ACTION] = "";
			$links[AppBase::MOVE_THREAD_VIEW_ACTION] = "";

			switch ($this->action) {
				case AppBase::FORUM_VIEW_ACTION:
					$links[AppBase::FORUM_VIEW_ACTION]["buttons"]["new_thread"] = "<a data-forum-id='" . $this->record . "' class='btn btn-warning' href='" . ForumHelper::getLink(AppBase::NEW_THREAD_VIEW_ACTION, $this->record) . "'><i class='fa fa-plus'></i>&nbsp;Start Topic &nbsp;</a>";
					$links[AppBase::FORUM_VIEW_ACTION]["tools"]["new_thread"] = "<a data-forum-id='" . $this->record . "' class='' href='" . ForumHelper::getLink(AppBase::NEW_THREAD_VIEW_ACTION, $this->record) . "'><i class='fa fa-plus'></i>&nbsp;Start Topic &nbsp;</a>";
					$links[AppBase::FORUM_VIEW_ACTION]["tools"]["forum_rss"] = "<a data-forum-id='" . $this->record . "' class='' href='" . ForumHelper::getLink(AppBase::RSS_FORUM_ACTION, $this->record) . "'><i class='fa fa-rss orange'></i>&nbsp;RSS Feed &nbsp;</a>";
					break;
				case AppBase::THREAD_VIEW_ACTION:
					$thread = $this->helper->getThread($this->record);

					$links[AppBase::THREAD_VIEW_ACTION]["buttons"]["new_post"] = "<a data-thread-id='" . $this->record . "' class='btn btn-warning' href='" . ForumHelper::getLink(AppBase::NEW_POST_VIEW_ACTION, $this->record) . "'><i class='fa fa-reply'></i>&nbsp;Post reply &nbsp;</a>";
					$links[AppBase::THREAD_VIEW_ACTION]["tools"]["new_post"] = "<a data-thread-id='" . $this->record . "' class='' href='" . ForumHelper::getLink(AppBase::NEW_POST_VIEW_ACTION, $this->record) . "'><i class='fa fa-reply'></i>&nbsp;Post reply &nbsp;</a>";
					$links[AppBase::THREAD_VIEW_ACTION]["tools"]["thread_rss"] = '<a href="' . ForumHelper::getLink(AppBase::RSS_THREAD_ACTION, $thread["id"]) . '" class=""><i class="fa fa-rss orange"></i>&nbsp;RSS Feed &nbsp;</a></span>';

					if ($current_user_id == $thread["user_id"] or current_user_can('manage_options')) {

						$links[AppBase::THREAD_VIEW_ACTION]["tools"]["edit"] = "<a data-thread-id='" . $this->record . "' class='' href='" . ForumHelper::getLink(AppBase::EDIT_THREAD_VIEW_ACTION, $this->record) . "'><i class='fa fa-edit'></i>&nbsp;Edit &nbsp;</a>";

						if ($thread["is_question"] && !$thread["is_solved"]) {
							$links[AppBase::THREAD_VIEW_ACTION]["tools"]["mark_solved"] = "<a data-nonce='$nonce' data-thread-id='$this->record' class='marksolved' href='javascript:void(0)'><i class='fa fa-check'></i>&nbsp;Mark question solved &nbsp;</a>";
						}
						if (current_user_can('manage_options') and $thread["status"] != "closed") {
							$links[$this->action]["tools"]["close"] = "<a data-nonce='$nonce' data-thread-id='$this->record' class='close_thread' href='javascipt:void(0)'><i class='fa fa-remove'></i>&nbsp;Close &nbsp;</a>";
						}
						if ($thread["status"] == "closed") {
							unset($links[AppBase::THREAD_VIEW_ACTION]["tools"]["new_post"]);
							unset($links[AppBase::THREAD_VIEW_ACTION]["tools"]["close"]);
							unset($links[AppBase::THREAD_VIEW_ACTION]["tools"]["edit"]);
						}
					}
					break;
			}
		} else {
			$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$links[$this->action]["buttons"]["login"] = "<a class='btn btn-success' href='" . wp_login_url($url) . "'><i class='fa fa-key'></i>&nbsp;Login &nbsp;</a>";
			$links[$this->action]["buttons"]["register"] = "<a class='btn btn-info' href='" . wp_registration_url() . "'><i class='fa fa-user'></i>&nbsp;Register &nbsp;</a>";
		}
		$this->smarty->assign("buttons", $links[$this->action]);

	}

	/*
	* @param
	* @return
	*/
	public function assignMisc()
	{

		$message = "You need have to register before you can post: click the register link below to proceed. To start viewing messages,	select the forum that you want to visit from the selection below.";
		$config = array(
			"date_format" => get_option(AppBase::OPTION_DATE_FORMAT),
			"images_dir" => plugins_url("assets/images", __FILE__),
		);
		$this->smarty->assign("border", AppBase::$border);
		$this->smarty->assign("forum_table_class", "forum-table");
		$this->smarty->assign("config", $config);

		if (!is_user_logged_in())
			$this->smarty->assign("message", $message);

	}

	public static function getThreadRSS()
	{
		$smarty = new Smarty();
		$data = ForumHelper::getInstance()->getPostsInThreadForRSS($_REQUEST["record"]);
		$smarty->assign("data", $data);
		return $smarty->fetch(WPFPATH . "/tpls" . "/rss_thread.xml");
	}

	public static function getForumRSS()
	{
		$smarty = new Smarty();
		$data = ForumHelper::getInstance()->getThreadsInForumForRSS($_REQUEST["record"]);
		$smarty->assign("data", $data);
		return $smarty->fetch(WPFPATH . "/tpls" . "/rss_forum.xml");
	}

	/*
	* @param
	* @return string
	*/
	public function getForumView()
	{
		$threads = $this->helper->getThreadsInForum($this->record, $this->offset);
		$this->smarty->assign("data", $threads);
		return $this->smarty->fetch($this->template_dir . "/threads.tpl");
	}

	/*
	* @param $action string
	* @param $record string
	* @return string
	*/
	public function getTopicView()
	{
		$posts = $this->helper->getPostsInThread($this->record, $this->offset);
		$this->helper->updateThreadViewCount($this->record);
		$this->smarty->assign("data", $posts);
		return $this->smarty->fetch($this->template_dir . "/posts.tpl");
	}

	public static function _exit($msg = "")
	{
		wp_die("
			<h1>Oops...</h1>
			<p>The requested forum resource was not found.<br>
			Please take a look at the forum main page or do a search</p>

			<ul>
				<li><a href='" . get_permalink() . "'>Forum main page</a></li>
				<li><a href='#'>Search the forums</a></li>
			</ul>"
		);
	}

	/*
	* @param $action string
	* @param $record string
	* @return string
	*/
	public function getMainView()
	{
		$cats = $this->helper->getCategories();
		$this->smarty->assign("data", $cats);
		return $this->smarty->fetch($this->template_dir . "/main.tpl");
	}
}

?>
