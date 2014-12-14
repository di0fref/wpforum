jQuery(function ($) {

	//$("#bbcode").markItUp(bbcodeSettings);

	$("#forum-form-new-thread").validate();
	$("#forum-form-new-post").validate();

	$(".marksolved").on("click", function () {
		if (confirm("Do you want to mark this topic as solved?")) {
			var record = $(this).data("thread-id");
			var post_id = $(this).data("post-id");
			var nonce = $(this).data("nonce");
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
		return false;
	});

});


