<?php
require_once("assets/guid.php");
require_once("ForumHelper.php");

global $wpdb;
/* Sanitize input */
$thread_id = ForumHelper::input_filter($_REQUEST["record"]);
$subject = ForumHelper::input_filter($_REQUEST["subject"]);
$text = ForumHelper::input_filter($_REQUEST["text"]);

$user_id = get_current_user_id();
$date = date("Y-m-d H:i:s");
$nr = ForumHelper::getNextPostNr($thread_id);
/* Add thread */
$post_id = create_guid();

$sql_post = "INSERT INTO " . AppBase::$posts_table . "
	(
		nr,
		subject,
		id,
		text,
		parent_id,
		date,
		user_id
	)
	VALUES(
		'$nr',
		'$subject',
		'$post_id',
		'$text',
		'$thread_id',
		'$date',
		'$user_id'
		)";

$wpdb->query($sql_post);
$page = ForumHelper::getInstance()->getTotalPages(AppBase::THREAD_VIEW_ACTION, $thread_id);
$redirect_url = ForumHelper::getLink(AppBase::THREAD_VIEW_ACTION, $thread_id, array(AppBase::FORUM_PAGE, $page."#post-{$post_id}"));
