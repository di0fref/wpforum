jQuery(function ($) {

	//$("#bbcode").markItUp(bbcodeSettings);

	$("#forum-form-new-thread").validate();
	$("#forum-form-new-post").validate();
	$("#forum-form-edit-thread").validate();
	$("#forum-form-edit-post").validate();

	$("#is_solved").change(function(){
		if(this.checked){
			$("#is_question").attr("checked", true);
		}
	});

	$("#is_question").change(function(){
		if(!this.checked){
			$("#is_solved").attr("checked", false);
		}
	});



	$(".marksolved").confirm({
		confirm: function (button) {
			mark_solved(button);
		}
	});

	$(".close_thread").confirm({
		confirm: function (button) {
			close_thread(button);
		}
	});

	$(".deletepost").confirm({
		confirm: function (button) {
			delete_post(button);
		}
	});

	$(".deletethread").confirm({
		confirm: function (button) {
			delete_thread(button);
		}
	});
	function delete_thread(button) {
		var thread_id = $(button).data("thread-id");
		var nonce = $(button).data("nonce");
		$.ajax({
			url: forumAjax.ajaxurl,
			dataType: "json",
			async: false,
			data: {
				action: "deletethread",
				record: thread_id,
				nonce: nonce
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			},
			success: function (response) {
				location.reload();
			}
		});
	}

	function delete_post(button) {
		var post_id = $(button).data("post-id");
		var nonce = $(button).data("nonce");
		$.ajax({
			url: forumAjax.ajaxurl,
			dataType: "json",
			async: false,
			data: {
				action: "deletepost",
				record: post_id,
				nonce: nonce
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			},
			success: function (response) {
				location.reload();
			}
		});
	}

	function mark_solved(button) {
		var record = $(button).data("thread-id");
		var post_id = $(button).data("post-id");
		var nonce = $(button).data("nonce");
		$.ajax({
			url: forumAjax.ajaxurl,
			dataType: "json",
			async: false,
			data: {
				action: "marksolved",
				record: record,
				fpost: post_id,
				nonce: nonce
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			},
			success: function (response) {
				location.reload();
			}
		});
	}


	function close_thread(button) {
		var record = $(button).data("thread-id");
		var post_id = $(button).data("post-id");
		var nonce = $(button).data("nonce");
		$.ajax({
			url: forumAjax.ajaxurl,
			dataType: "json",
			async: false,
			data: {
				action: "closethread",
				record: record,
				fpost: post_id,
				nonce: nonce
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			},
			success: function (response) {
				location.reload();
			}
		});
	}

});


