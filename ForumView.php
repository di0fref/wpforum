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

	function permission()
	{
		$message = "You do not have permission to view this page.";
		ForumHelper::getInstance()->clearMessages();
		ForumHelper::getInstance()->addMessage($message, "danger");
		$messages = ForumHelper::getInstance()->getMessages();
		if (is_array($messages)) {
			$this->smarty->assign("messages", $messages);
			ForumHelper::getInstance()->clearMessages();
		}
		return $this->smarty->fetch($this->template_dir . "/permission.tpl");
	}

	public function getSearchView()
	{
		if (isset($_REQUEST["forum-search"]) or isset($_REQUEST["tag"])) {
			//AppBase::verifyNonce(AppBase::WPFORUM_INSERT_NONCE);
			if (isset($_REQUEST["tag"])) {
				$results = ForumHelper::getInstance()->getTagResults();
				$this->smarty->assign("search_frase", "tag: " . $_REQUEST["tag"]);
			} else {
				$results = ForumHelper::getInstance()->getSearchResults();
				$this->smarty->assign("search_frase", "'" . $_REQUEST["search_term"] . "'");
			}
			$this->smarty->assign("data", $results);
			return $this->smarty->fetch($this->template_dir . "/search_result.tpl");
		}
		$this->smarty->assign("nonce", wp_create_nonce("wpforum_insert_nonce"));
		return $this->smarty->fetch($this->template_dir . "/search.tpl");
	}

	function getNewThreadView()
	{
		$forum = $this->helper->getForum($_REQUEST["record"]);
		if (!$forum) {
			self::_exit();
		}
		$this->smarty->assign("formbuttons", $this->formButtons());
		$this->smarty->assign("record", $_REQUEST["record"]);
		$this->smarty->assign("nonce", wp_create_nonce("wpforum_insert_nonce"));
		return $this->smarty->fetch($this->template_dir . "/new_thread_form.tpl");
	}

	function getEditPostView()
	{
		$post = $this->helper->getPost($_REQUEST["record"]);

		$post["text"] = stripslashes($post["text"]);
		$post["subject"] = stripslashes($post["subject"]);

		$this->smarty->assign("formbuttons", $this->formButtons());
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
		$this->smarty->assign("formbuttons", $this->formButtons());
		$this->smarty->assign("statusDD", $this->helper->getStatusDD($thread["status"]));
		$this->smarty->assign("nonce", wp_create_nonce("wpforum_insert_nonce"));
		$this->smarty->assign("thread", $thread);
		$this->smarty->assign("tags", implode(",", ForumHelper::getInstance()->getTags($thread["id"], false)));
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
		$this->smarty->assign("formbuttons", $this->formButtons());
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

	function formButtons()
	{
		$button = "<a title='" . __("Bold", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[b]\", \"[/b]\", 			document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/b.png' 	/></a>\n";    //align='bottom' width='23' height='22' alt='Bold' 		title='Bold' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("Italic", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[i]\", \"[/i]\", 		document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/i.png' 	/></a>\n";    //align='bottom' width='23' height='22' alt='Italic' 		title='Italic' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("Underline", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[u]\", \"[/u]\", 	document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/u.png' 	/></a>\n";    //align='bottom' width='23' height='22' alt='Underline' 	title='Underline' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("Code", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[code]\", \"[/code]\", 	document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/code.png' 	/></a>\n";    //align='bottom' width='23' height='22' alt='Code' 		title='Code' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("Quote", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[quote]\", \"[/quote]\",document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/quote.png' /></a>\n";    //align='bottom' width='23' height='22' alt='Quote' 		title='Quote' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("List", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[list]\", \"[/list]\", 	document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/list.png' 	/></a>\n";    //align='bottom' width='23' height='22' alt='List' 		title='List' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("List item", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[*]\", \"\", 		document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/li.png' 	/></a>\n";    //align='bottom' width='23' height='22' alt='List' 		title='List' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("Link", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[url]\", \"[/url]\", 	document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/url.png' 	/></a>\n";    //align='bottom' width='23' height='22' alt='Link' 		title='Link' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("Image", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[img]\", \"[/img]\", 	document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/img.png' 	/></a>\n";    //align='bottom' width='23' height='22' alt='Image' 		title='Image' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";
		$button .= "<a title='" . __("Email", "wpforum") . "'href='javascript:void(0);' onclick='surroundText(\"[email]\", \"[/email]\",document.forms.forum_form.text); return false;'><img src='/wp-content/plugins/wpforum/assets/images/buttons/email.png' /></a>\n";    //align='bottom' width='23' height='22' alt='Image' 		title='Image' style='background-image: url($this->skin_url/images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;' /></a>\n";

		return $button;
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
			$links[AppBase::POST_VIEW_ACTION] = "";

			switch ($this->action) {
				case AppBase::FORUM_VIEW_ACTION:
					$links[AppBase::FORUM_VIEW_ACTION]["buttons"]["new_thread"] = "<a data-forum-id='" . $this->record . "' class='pull-left btn btn-warning' href='" . ForumHelper::getLink(AppBase::NEW_THREAD_VIEW_ACTION, $this->record) . "'><i class='fa fa-plus'></i>&nbsp;Start Topic &nbsp;</a>";
					$links[AppBase::FORUM_VIEW_ACTION]["tools"]["new_thread"] = "<a data-forum-id='" . $this->record . "' class='' href='" . ForumHelper::getLink(AppBase::NEW_THREAD_VIEW_ACTION, $this->record) . "'><i class='fa fa-plus'></i>&nbsp;Start Topic &nbsp;</a>";
					$links[AppBase::FORUM_VIEW_ACTION]["tools"]["forum_rss"] = "<a data-forum-id='" . $this->record . "' class='' href='" . ForumHelper::getLink(AppBase::RSS_FORUM_ACTION, $this->record) . "'><i class='fa fa-rss orange'></i>&nbsp;RSS Feed &nbsp;</a>";
					break;
				case AppBase::THREAD_VIEW_ACTION:
					$thread = $this->helper->getThread($this->record);

					$links[AppBase::THREAD_VIEW_ACTION]["buttons"]["new_post"] = "<a data-thread-id='" . $this->record . "' class='pull-left btn btn-warning' href='" . ForumHelper::getLink(AppBase::NEW_POST_VIEW_ACTION, $this->record) . "'><i class='fa fa-reply'></i>&nbsp;Post reply &nbsp;</a>";
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
						}
					}
					break;
			}
			$links[$this->action]["tools"]["search"] = "<a href='".ForumHelper::getLink(AppBase::SEARCH_VIEW_ACTION)."'><i class='fa fa-search'></i>&nbsp;Advanced Search &nbsp;</a>";

			$this->smarty->assign("action", ForumHelper::getLink(AppBase::SEARCH_VIEW_ACTION));
			$links[$this->action]["buttons"]["search"] = $this->smarty->fetch($this->template_dir . "/search_form.tpl");

		} else {
			$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$links[$this->action]["buttons"]["login"] = "<span class='pull-right'><a class='btn btn-success' href='" . wp_login_url($url) . "'><i class='fa fa-lock'></i>&nbsp;Login &nbsp;</a></span>";
			$links[$this->action]["buttons"]["register"] = "<span class='pull-right'><a class='btn btn-info' href='" . wp_registration_url() . "'><i class='fa fa-user'></i>&nbsp;Register &nbsp;</a></span>";
		}
		$this->smarty->assign("buttons", $links[$this->action]);
	}

	/*
	* @param
	* @return
	*/
	public function assignMisc()
	{
		global $appBase;
		$message = "You need to be logged in before you can post: click the register or login link below to proceed.";
		$config = array(
			"date_format" => get_option(AppBase::OPTION_DATE_FORMAT),
			"images_dir" => plugins_url("assets/images", __FILE__),
		);
		$this->smarty->assign("border", AppBase::$border);
		$this->smarty->assign("config", $config);

		if (!is_user_logged_in()) {
			ForumHelper::getInstance()->addMessage($message, "warning");
		}
		$messages = ForumHelper::getInstance()->getMessages();
		if (is_array($messages)) {
			$this->smarty->assign("messages", $messages);
			ForumHelper::getInstance()->clearMessages();
		}

		$options = array(
			"display_pagination_top" => get_option(AppBase::OPTION_DISPLAY_PAGINATION_TOP)
		);
		$this->smarty->assign("options", $options);
		$this->smarty->assign("pagination", $appBase->getPagination());

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
		$this->smarty->assign("tags", ForumHelper::getInstance()->getTagList());
		return $this->smarty->fetch($this->template_dir . "/main.tpl");
	}
}

?>
