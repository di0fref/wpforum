<?php

global $wpdb;

$sql_forums = "DELETE FROM " . AppBase::$forums_table . " WHERE id = '{$_REQUEST["forum"]}'";
$sql_threads = "DELETE FROM " . AppBase::$threads_table . " WHERE parent_id = '{$_REQUEST["forum"]}'";

$post_ids = array();

$threads = ForumHelper::getInstance()->getThreads($_REQUEST["forum"]);
foreach ($threads as $thread) {
	$posts = ForumHelper::getInstance()->getPosts($thread["id"]);
	foreach ($posts as $post) {
		$post_ids[] = $post["id"];
	}

}
$sql_posts = "DELETE FROM " . AppBase::$posts_table . " WHERE id IN('" . implode("','", $post_ids) . "')";

$wpdb->query($sql_forums);
$wpdb->query($sql_threads);
$wpdb->query($sql_posts);

echo "<h2>Forum deleted</h2>";

echo "<a href='" . admin_url("admin.php?page=wpforum-submenu-manage") . "'>Back</a>";
