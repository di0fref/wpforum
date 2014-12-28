<?php
require_once("assets/guid.php");
require_once("ForumHelper.php");

global $wpdb;
/* Sanitize input */
$thread_id = ForumHelper::input_filter($_REQUEST["thread_id"]);
$post_id = ForumHelper::input_filter($_REQUEST["record"]);
$subject = ForumHelper::input_filter($_REQUEST["subject"]);
$text = ForumHelper::input_filter($_REQUEST["text"]);


$sql = "UPDATE " . AppBase::$posts_table. " SET subject = '$subject', text='$text' WHERE id = '$post_id'";

$wpdb->query($sql);

$redirect_url = ForumHelper::getLink(AppBase::THREAD_VIEW_ACTION, $thread_id);

?>

