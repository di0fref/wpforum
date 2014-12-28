<?php
require_once("assets/Smarty/libs/Smarty.class.php");

/*
* Class:
* Author: Fredrik Fahlstad
*/

class WPForumAjax
{
	protected $smarty;
	protected $template_dir;
	protected $helper;

	function __construct()
	{
		$this->smarty = new Smarty();
		$this->template_dir = WPFPATH . "/tpls";
		$this->helper = new ForumHelper();
	}

	function marksolved()
	{
		$this->checkInput();
		AppBase::checkParams($_REQUEST[AppBase::RECORD], "guid");
		$post_id = "";
		if (isset($_REQUEST[AppBase::FORUM_POST])) {
			AppBase::checkParams($_REQUEST[AppBase::FORUM_POST], "guid");
			$post_id = $_REQUEST[AppBase::FORUM_POST];
		}
		$result = ForumHelper::markSolved($_REQUEST[AppBase::RECORD], $post_id);
		$response = array(
			"affected_rows" => $result,
		);
		die(json_encode($response));
	}

	function closethread()
	{
		$this->checkInput();
		AppBase::checkParams($_REQUEST[AppBase::RECORD], "guid");
		$result = ForumHelper::closeThread($_REQUEST[AppBase::RECORD]);
		$response = array(
			"affected_rows" => $result,
		);
		die(json_encode($response));
	}

	function deletepost()
	{
		$this->checkInput();
		AppBase::checkParams($_REQUEST[AppBase::RECORD], "guid");
		$result = ForumHelper::deletePost($_REQUEST[AppBase::RECORD]);
		$response = array(
			"affected_rows" => $result,
		);
		die(json_encode($response));
	}

	function deletethread()
	{
		$this->checkInput();
		AppBase::checkParams($_REQUEST[AppBase::RECORD], "guid");
		$result = ForumHelper::deleteThread($_REQUEST[AppBase::RECORD]);
		$response = array(
			"affected_rows" => $result,
		);
		die(json_encode($response));
	}

	function checkInput()
	{
		if (!wp_verify_nonce($_REQUEST['nonce'], "wpforum_ajax_nonce")) {
			exit("No naughty business please");
		}
	}

}