jQuery(function ($) {

	setTimeout(function() {
		$(".alert-success").hide('blind', {}, 500)
	}, 5000);

	$("#forum-form-new-thread").validate();
	$("#forum-form-new-post").validate();
	$("#forum-form-edit-thread").validate();
	$("#forum-form-edit-post").validate();

	$("#is_solved").change(function () {
		if (this.checked) {
			$("#is_question").attr("checked", true);
		}
	});

	$("#is_question").change(function () {
		if (!this.checked) {
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

// Surrounds the selected text with text1 and text2.
function surroundText(text1, text2, textarea) {
	// Can a text range be created?
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange) {
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

		if (temp_length == 0) {
			caretPos.moveStart("character", -text2.length);
			caretPos.moveEnd("character", -text2.length);
			caretPos.select();
		}
		else
			textarea.focus(caretPos);
	}
	// Mozilla text range wrap.
	else if (typeof(textarea.selectionStart) != "undefined") {
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text1 + selection + text2 + end;

		if (textarea.setSelectionRange) {
			if (selection.length == 0)
				textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
			else
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			textarea.focus();
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put them on the end, then.
	else {
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
	}
}

function goBack() {
	window.history.back()
}