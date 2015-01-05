<?php

global $wpdb;

$sql_cats = "DELETE FROM " . AppBase::$categories_table . " WHERE id = '{$_REQUEST["category"]}'";
$sql_forums = "DELETE FROM " . AppBase::$forums_table . " WHERE parent_id = '{$_REQUEST["category"]}'";

$thread_ids = array();
$post_ids = array();

$forums = ForumHelper::getInstance()->getForumsInCategory($_REQUEST["category"]);
foreach ($forums as $forum) {
	$threads = ForumHelper::getInstance()->getThreads($forum["id"]);
	foreach ($threads as $thread) {
		$thread_ids[] = $thread["id"];
		$posts = ForumHelper::getInstance()->getPosts($thread["id"]);
		foreach($posts as $post){
			$post_ids[] = $post["id"];
		}
	}
}
$sql_threads = "DELETE FROM ". AppBase::$threads_table . " WHERE id IN('".implode("','", $thread_ids)."')";
$sql_posts = "DELETE FROM ". AppBase::$posts_table . " WHERE id IN('".implode("','", $post_ids)."')";


$wpdb->query($sql_cats);
$wpdb->query($sql_forums);
$wpdb->query($sql_threads);
$wpdb->query($sql_posts);


echo "<h2>Category deleted</h2>";

echo "<a href='" . admin_url("admin.php?page=wpforum-submenu-manage") . "'>Back</a>";
