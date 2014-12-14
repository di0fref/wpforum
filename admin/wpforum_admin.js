jQuery(document).ready(function ($) {

	$("#wpforum_admin_options_form").validate({
		rules: {
			threads_per_page: {
				required: true,
				digits: true
			},
			posts_per_page: {
				required: true,
				digits: true
			},
			date_format: {
				required: true
			}
		}
	});

	$("#add_category_form").validate({
		rules:{
			name:{
				required: true
			},
			sort_order:{
				required: true,
				digits: true
			}
		}
	});
	$("#add_forum_form").validate({
		rules:{
			name:{
				required: true
			},
			sort_order:{
				required: true,
				digits: true
			}
		}
	});
});