<?php
/*
	Plugin Name: WP-Forum 3
	Plugin Author: Fredrik Fahlstad
	Plugin URI: http://www.fahlstad.se
	Author URI: http://www.fahlstad.se
	Version: 3.0
*/
include_once("AppBase.php");
include_once("WPForumAjax.php");
include_once("ForumHelper.php");

$appBase = new AppBase();
$ajax = new WPForumAjax();

// Activating?
register_activation_hook(__FILE__, array(&$appBase, 'install'));
add_action("the_content", array(&$appBase, "main"));
add_action("wp_head", array(&$appBase, "head"));
add_action("wp_enqueue_scripts", array(&$appBase, "enqueue_scripts"));
add_action("init", array(&$appBase, "init"));
add_action('template_redirect', array(&$appBase, "processForm"));

/* Ajax action */
add_action("wp_ajax_marksolved", array(&$ajax, 'marksolved'));

/* Admin */
add_action('admin_enqueue_scripts', 'wpforum_admin_enqueue_scripts', '999');
add_action('admin_init', 'wpforum_admin_init');

function wpforum_admin_init()
{
	add_action('admin_post_wpforum_add_category', 'wpforum_admin_process_form');
	add_action('admin_post_wpforum_edit_category', 'wpforum_admin_process_form');

	add_action('admin_post_wpforum_add_forum', 'wpforum_admin_process_form');
	add_action('admin_post_wpforum_edit_forum', 'wpforum_admin_process_form');
}

function wpforum_admin_process_form()
{
	switch($_REQUEST["action"]){
		case "wpforum_add_category":
			ForumHelper::add_category($_REQUEST["name"],$_REQUEST["description"], $_REQUEST["sort_order"]);
			break;
		case "wpforum_add_forum":
			ForumHelper::add_forum($_REQUEST["name"],$_REQUEST["description"], $_REQUEST["sort_order"], $_REQUEST["category"]);
			break;
		case "wpforum_edit_category":
			ForumHelper::update_category($_REQUEST["id"], $_REQUEST["name"],$_REQUEST["description"], $_REQUEST["sort_order"]);
			break;
		case "wpforum_edit_forum":
			ForumHelper::update_forum($_REQUEST["forum"],$_REQUEST["name"],$_REQUEST["description"], $_REQUEST["sort_order"], $_REQUEST["category_id"]);
			break;
	}
	wp_redirect(admin_url('admin.php?page=wpforum-submenu-manage'));
}

function wpforum_admin_enqueue_scripts()
{
	wp_register_script('wpforum_admin_validate', plugins_url('assets/js/jquery.validate.min.js', __FILE__), array("jquery.validate"), '', false);
	wp_register_script('wpforum_admin_js', plugins_url('admin/wpforum_admin.js', __FILE__), array("jquery"), '', false);
	wp_enqueue_script('wpforum_admin_js');
	wp_enqueue_script('wpforum_admin_validate');
}

function wpforum_menu()
{
	add_menu_page('WP-Forum', 'WP-Forum', 'manage_options', 'wpforum-menu', 'wpforum_options');
	add_submenu_page('wpforum-menu', 'Categories/Forums', 'Categories/Forums', 'manage_options', 'wpforum-submenu-manage', 'wpforum_manage');

	add_submenu_page(null, 'Add Category', 'Add Category', 'manage_options', 'wpforum-add-category', 'wpforum_add_category');
	add_submenu_page(null, 'Edit Category', 'Edit Category', 'manage_options', 'wpforum-edit-category', 'wpforum_edit_category');

	add_submenu_page(null, 'Add Forum', 'Add Forum', 'manage_options', 'wpforum-add-forum', 'wpforum_add_forum');
	add_submenu_page(null, 'Edit Forum', 'Edit Forum', 'manage_options', 'wpforum-edit-forum', 'wpforum_edit_forum');

}

add_action('admin_menu', 'wpforum_menu');
add_action('admin_init', 'wpforum_register_settings');

function wpforum_options()
{
	include('admin/wpforum-admin.php');
}

function wpforum_manage()
{
	include('admin/wpforum_manage.php');
}

function wpforum_add_category()
{
	include('admin/wpforum_add_category.php');
}

function wpforum_edit_category()
{
	include('admin/wpforum_edit_category.php');
}

function wpforum_add_forum()
{
	include('admin/wpforum_add_forum.php');
}

function wpforum_edit_forum()
{
	include('admin/wpforum_edit_forum.php');
}

function wpforum_register_settings()
{
	register_setting('wpforum-settings-group', 'wpforum_threads_per_page');
	register_setting('wpforum-settings-group', 'wpforum_posts_per_page');
	register_setting('wpforum-settings-group', 'wpforum_date_format');
}
