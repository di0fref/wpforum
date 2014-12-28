<?php
require_once("assets/guid.php");
require_once("ForumHelper.php");

global $wpdb;
/* Sanitize input */
$thread_id = ForumHelper::input_filter($_REQUEST["thread_id"]);
$subject = ForumHelper::input_filter($_REQUEST["subject"]);
$is_question = ForumHelper::input_filter($_REQUEST["is_question"]);
$is_solved = ForumHelper::input_filter($_REQUEST["is_solved"]);

$solved_post_adds = "";
if(!$is_solved){
	$solved_post_adds = " ,solved_post_id = ''";
}

$sql = "UPDATE " . AppBase::$threads_table. " SET is_solved='$is_solved', subject = '$subject', is_question='$is_question' $solved_post_adds WHERE id = '$thread_id'";

$wpdb->query($sql);

$redirect_url = ForumHelper::getLink(AppBase::THREAD_VIEW_ACTION, $thread_id);

?>

