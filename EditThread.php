<?php
require_once("assets/guid.php");
require_once("ForumHelper.php");
global $wpdb;
/* Sanitize input */
$tags = explode(",", ForumHelper::input_filter($_REQUEST["tags"]));
$thread_id = ForumHelper::input_filter($_REQUEST["thread_id"]);
$subject = ForumHelper::input_filter($_REQUEST["subject"]);

$is_question = isset($_REQUEST["is_question"])?ForumHelper::input_filter($_REQUEST["is_question"]):0;
$is_solved = isset($_REQUEST["is_solved"])?ForumHelper::input_filter($_REQUEST["is_solved"]):0;
$sticky = isset($_REQUEST["sticky"])?ForumHelper::input_filter($_REQUEST["sticky"]):0;
$status = ForumHelper::input_filter($_REQUEST["status"]);

$solved_post_adds = "";
if(!$is_solved){
	$solved_post_adds = " ,solved_post_id = ''";
}

$sql = "UPDATE " . AppBase::$threads_table. " SET status='$status', sticky='$sticky',  is_solved='$is_solved', subject = '$subject', is_question='$is_question' $solved_post_adds WHERE id = '$thread_id'";

$wpdb->query($sql);

$tag_ids = array();
foreach ($tags as $key => $tag){
	$tag_ids[] = ForumHelper::getInstance()->addTag($tag);
}

ForumHelper::getInstance()->addTagsToThread($tag_ids, $thread_id);

ForumHelper::getInstance()->addMessage("Topic updated", "success");

$redirect_url = ForumHelper::getLink(AppBase::THREAD_VIEW_ACTION, $thread_id);

?>

