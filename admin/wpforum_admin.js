jQuery(document).ready(function ($) {


	$(".admin_delete_thread").confirm({
		confirm: function (button) {
			var url = $(button).data("url");
			location.href = url;
		}
	});
	$(".admin_delete_category").confirm({
		confirm: function (button) {
			var url = $(button).data("url");
			location.href = url;
		}
	});

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
		rules: {
			name: {
				required: true
			},
			sort_order: {
				required: true,
				digits: true
			}
		}
	});
	$("#add_forum_form").validate({
		rules: {
			name: {
				required: true
			},
			sort_order: {
				required: true,
				digits: true
			}
		}
	});
});