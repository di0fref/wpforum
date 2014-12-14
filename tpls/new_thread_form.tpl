<div class="forum-trail">{$trail}</div>
<h3>Start New Topic</h3>
<!--<form name="forum-form-new-thread" id="forum-form-new-thread" method="post">
	<table class="" border="0" width="100%">
		<tr>
			<td>
				<label for="subject">Subject:</label>
				<input style="width:200px;" type="text" name="subject" required>
			</td>
		</tr>
		<tr>
			<td>
				<label for="is_question">This is a question:</label>
				<input type="checkbox" name="is_question" value="1"></td>
		</tr>
		<tr>
			<td><textarea style="width:500px; height:200px" name="text" id="bbcode" required></textarea></td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="forum-form-new-thread" value="Submit New Topic">
			</td>
		</tr>
	</table>
	<input type="hidden" name="record" value="{$record}">
	<input type="hidden" name="nonce" value="{$nonce}">
</form>-->
<div style="width: 500px">
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
		<textarea  rows="10" class="form-control" name="text" id="_bbcode" required></textarea>
	</div>
	<button type="submit" class="btn btn-default" name="forum-form-new-thread">Submit</button>
	<input type="hidden" name="record" value="{$record}">
	<input type="hidden" name="nonce" value="{$nonce}">
</form>
</div>
