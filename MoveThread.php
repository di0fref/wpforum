<?php

global $wpdb;

$thread_id = ForumHelper::input_filter($_REQUEST["thread_id"]);
$forum_id = ForumHelper::input_filter($_REQUEST["forum_id"]);
$original_forum_id = ForumHelper::input_filter($_REQUEST["original_forum_id"]);

if($original_forum_id != $forum_id) {
	$sql = "UPDATE " . AppBase::$threads_table . " SET parent_id = '$forum_id', moved_from = '$original_forum_id' WHERE id= '$thread_id'";
	$wpdb->query($sql);
}

$redirect_url = ForumHelper::getLink(AppBase::FORUM_VIEW_ACTION, $original_forum_id);

?>