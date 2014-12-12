<?php

require_once("assets/guid.php");
require_once("ForumHelper.php");

global $wpdb;
/* Sanitize input */
$forum_id = ForumHelper::input_filter($_REQUEST["record"]);
$subject = ForumHelper::input_filter($_REQUEST["subject"]);
$text = ForumHelper::input_filter($_REQUEST["text"]);

$is_question = 0;
if (isset($_REQUEST["is_question"])) {
	$is_question = ForumHelper::input_filter($_REQUEST["is_question"]);
}

$user_id = get_current_user_id();
$date = date("Y-m-d H:i:s");

/* Add thread */
$thread_id = create_guid();
$sql_thread = "
	INSERT INTO " . AppBase::$threads_table . "
		(
			id,
			subject,
			parent_id,
			date,
			status,
			is_question,
			user_id
			)
	VALUES(
		'$thread_id',
		'$subject',
		'$forum_id',
		'$date',
		'open',
		'$is_question',
		'$user_id'
	)";

/* Add Post */
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
		'1',
		'$subject',
		'$post_id',
		'$text',
		'$thread_id',
		'$date',
		'$user_id'
		)";
/*
echo "<pre>";
print_r($sql_thread);
echo "</pre>";
echo "<pre>";
print_r($sql_post);
echo "</pre>";die;
*/
$wpdb->query($sql_thread);
$wpdb->query($sql_post);

$redirect_url = ForumHelper::getLink(AppBase::THREAD_VIEW_ACTION, $thread_id);
