jQuery(function ($) {


	$("#bbcode").markItUp(bbcodeSettings);

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


	/*$(".new_thread").on("click", function () {
	 openPopup("newthread", $(this));
	 return false;
	 });
	 $(".new_post").on("click", function () {
	 return false;
	 });
	 $(".subscribe_rss").on("click", function () {
	 return false;
	 });
	 $(".subscribe_email").on("click", function () {
	 return false;
	 });

	 function openPopup(action, element) {

	 var data = {
	 action: action,
	 record: element.data("forum-id"),
	 nonce: element.data("nonce")
	 };

	 $("#forum-dialog").dialog({
	 modal: true,
	 width: "60%",
	 height: "auto",
	 title: "WP Forum",
	 position: {
	 my: "center",
	 at: "center",
	 of: $("body"),
	 within: $("body")
	 },
	 open: function () {
	 $(this).load(forumAjax.ajaxurl, data);
	 },
	 close: function (event, ui) {
	 $("#forum-dialog").html("");
	 },
	 buttons: {
	 OK: function () {
	 },
	 CANCEL: function () {
	 $(this).dialog("close");
	 }
	 }
	 });
	 }
	 */
});


