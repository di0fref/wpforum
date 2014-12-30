<div class="forum-trail">{$trail}</div>
<h3>Move Topic</h3>
<div class="forum-form">
	<form role="form" name="forum-form-move-thread" id="forum-form-move-thread" method="post">
		<div class="form-group">
			<label for="forum_id">Forum</label>
			<select id="forum_id" name="forum_id" class="form-control">
				{$forumDD}
			</select>
			<input type="hidden" name="nonce" value="{$nonce}">
		</div>
		<button type="submit" class="btn btn-default" name="forum-form-move-thread">Submit</button>
		<input type="hidden" name="thread_id" value="{$thread.id}">
		<input type="hidden" name="original_forum_id" value="{$forum.id}">
	</form>