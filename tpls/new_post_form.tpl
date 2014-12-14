<div class="forum-trail">{$trail}</div>
<h3>Reply to: {$thread_name}</h3>
<div style="width: 500px">
	<form name="forum-form-new-post" id="forum-form-new-post" method="post" role="form">

		<div class="form-group">
			<label for="subject">Subject</label>
			<input class="form-control" type="text" name="subject" required value="{if isset($quote_data.subject)}{$quote_data.subject}{/if}">
		</div>
		<div class="form-group">
			<textarea  rows="10" class="form-control" name="text" id="_bbcode" required>{if isset($quote_data.text)}{$quote_data.text}{/if}</textarea>
		</div>
		<button name="forum-form-new-post" type="submit" class="btn btn-default">Submit</button>

		<input type="hidden" name="record" value="{$record}">
		<input type="hidden" name="nonce" value="{$nonce}">
	</form>
</div>