<div class="forum-trail">{$trail}</div>
<h3>Editing: {$post.subject}</h3>
<div style="width: 500px">
	<form name="forum-form-edit-post" id="forum-form-edit-post" method="post" role="form">

		<div class="form-group">
			<label for="subject">Subject</label>
			<input class="form-control" type="text" name="subject" required value="{$post.subject}">
		</div>
		<div class="form-group">
			<textarea  rows="10" class="form-control" name="text" id="_bbcode" required>{$post.text}</textarea>
		</div>
		<button name="forum-form-edit-post" type="submit" class="btn btn-default">Update post</button>

		<input type="hidden" name="record" value="{$post.id}">
		<input type="hidden" name="thread_id" value="{$post.parent_id}">
		<input type="hidden" name="nonce" value="{$nonce}">
	</form>
</div>