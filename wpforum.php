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
add_action("register_activation_hook", array(&$appBase, "activation"));

add_action('template_redirect', array(&$appBase, "preHeader"));

/* Ajax action */
add_action("wp_ajax_marksolved", array(&$ajax, 'marksolved'));
add_action("wp_ajax_closethread", array(&$ajax, 'closethread'));
add_action("wp_ajax_deletethread", array(&$ajax, 'deletethread'));
add_action("wp_ajax_deletepost", array(&$ajax, 'deletepost'));
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
	switch ($_REQUEST["action"]) {
		case "wpforum_add_category":
			ForumHelper::add_category($_REQUEST["name"], $_REQUEST["description"], $_REQUEST["sort_order"]);
			break;
		case "wpforum_add_forum":
			ForumHelper::add_forum($_REQUEST["name"], $_REQUEST["description"], $_REQUEST["sort_order"], $_REQUEST["category"]);
			break;
		case "wpforum_edit_category":
			ForumHelper::update_category($_REQUEST["id"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["sort_order"]);
			break;
		case "wpforum_edit_forum":
			ForumHelper::update_forum($_REQUEST["forum"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["sort_order"], $_REQUEST["category_id"]);
			break;
	}
	wp_redirect(admin_url('admin.php?page=wpforum-submenu-manage'));
}

function wpforum_admin_enqueue_scripts($hook_suffix)
{
	if (strpos($hook_suffix, "wpforum") === false) {
		return;
	}
	wp_register_script('wpforum_admin_validate', plugins_url('assets/js/jquery.validate.min.js', __FILE__), array("jquery"), '', false);
	wp_register_script('wpforum_confirm', plugins_url('assets/js/jquery.confirm/jquery.confirm.js', __FILE__), array("jquery", "bootstrap"), '', false);
	wp_register_script('wpforum_admin_js', plugins_url('admin/wpforum_admin.js', __FILE__), array("jquery","wpforum_admin_validate", "wpforum_confirm"), '', false);
	wp_register_script('bootstrap', plugins_url('assets/bootstrap-3.3.1/js/bootstrap.min.js', __FILE__), array("jquery"), '3.3.1', false);

	wp_register_style('wpforum_bootstrap_styles', plugins_url('assets/bootstrap-3.3.1/css/bootstrap.min.css', __FILE__), array(), '3.3.1', 'all');
	wp_register_style('wpforum_bootstrap_styles_theme', plugins_url('assets/bootstrap-3.3.1/css/bootstrap-theme.min.css', __FILE__), array(), '3.3.1', 'all');
	wp_enqueue_style("wpforum_bootstrap_styles");
	wp_enqueue_style("wpforum_bootstrap_styles_theme");

	wp_enqueue_script('bootstrap');
	wp_enqueue_script('wpforum_admin_validate');
	wp_enqueue_script('wpforum_admin_js');
	wp_enqueue_script('wpforum_confirm');
}

function wpforum_menu()
{
	add_menu_page('WP-Forum', 'WP-Forum', 'manage_options', 'wpforum-menu', 'wpforum_options');
	add_submenu_page('wpforum-menu', 'Categories/Forums', 'Categories/Forums', 'manage_options', 'wpforum-submenu-manage', 'wpforum_manage');

	add_submenu_page(null, 'Add Category', 'Add Category', 'manage_options', 'wpforum-add-category', 'wpforum_add_category');
	add_submenu_page(null, 'Edit Category', 'Edit Category', 'manage_options', 'wpforum-edit-category', 'wpforum_edit_category');
	add_submenu_page(null, 'Delete Category', 'Delete Category', 'manage_options', 'wpforum-delete-category', 'wpforum_delete_category');

	add_submenu_page(null, 'Add Forum', 'Add Forum', 'manage_options', 'wpforum-add-forum', 'wpforum_add_forum');
	add_submenu_page(null, 'Edit Forum', 'Edit Forum', 'manage_options', 'wpforum-edit-forum', 'wpforum_edit_forum');
	add_submenu_page(null, 'Delete Forum', 'Delete Forum', 'manage_options', 'wpforum-delete-forum', 'wpforum_delete_forum');

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

function wpforum_delete_category()
{
	include('admin/wpforum_delete_category.php');
}

function wpforum_add_forum()
{
	include('admin/wpforum_add_forum.php');
}

function wpforum_edit_forum()
{
	include('admin/wpforum_edit_forum.php');
}

function wpforum_delete_forum()
{
	include('admin/wpforum_delete_forum.php');
}

function wpforum_register_settings()
{
	register_setting('wpforum-settings-group', AppBase::OPTION_THREADS_VIEW_COUNT);
	register_setting('wpforum-settings-group', AppBase::OPTION_POSTS_VIEW_COUNT);
	register_setting('wpforum-settings-group', AppBase::OPTION_DATE_FORMAT);
	register_setting('wpforum-settings-group', AppBase::OPTION_POST_THROTTLE_SECONDS);
	register_setting('wpforum-settings-group', AppBase::OPTION_DISPLAY_PAGINATION_TOP);
}

//associating a function to login hook
add_action('wp_login', 'set_last_login');

function set_last_login($login)
{
	$months = 60 * 60 * 24 * 60 + time();
	$user = get_user_by("login", $login);
	$last = get_last_login($user->ID);
	$last_formatted = strftime(get_option(AppBase::OPTION_DATE_FORMAT), strtotime($last));
	setcookie('lastVisit', $last, $months);
	update_user_meta($user->ID, 'last_login', current_time('mysql'));
	/* Make sure we have a session */
	if (!session_id()) {
		session_start();
	}
	ForumHelper::getInstance()->addMessage("Welcome $login, your last visit was on $last_formatted", "success");

}

function get_last_login($user_id)
{
	$last_login = get_user_meta($user_id, 'last_login', true);
	return $last_login;
}
