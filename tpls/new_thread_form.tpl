<div class="forum-trail">{$trail}</div>
<h3>Start New Topic</h3>
<div class="forum-form">
<form role="form" name="forum-form-new-thread" id="forum-form-new-thread" method="post">
	<div class="form-group">
		<label for="subject">Subject</label>
		<input class="form-control" type="text" name="subject" required>
	</div>
	<div class="checkbox">
		<label>
			<input name="is_question" value="1" type="checkbox"> This is a question
		</label>
	</div>
	<div class="form-group">
		<textarea  rows="10" class="form-control" name="text" id="text" required></textarea>
	</div>
	<button type="submit" class="btn btn-default" name="forum-form-new-thread">Submit</button>
	<input type="hidden" name="record" value="{$record}">
	<input type="hidden" name="nonce" value="{$nonce}">
</form>
</div>
