<div class="forum-trail">{$trail}</div>
<h3>Reply to: {$thread_name}</h3>

<form name="forum-form-new-post" id="forum-form-new-post" method="post">
	<table class="forum-table" border="0">
		<tr>
			<td>
				<textarea style="width:500px; height:200px" name="text" id="bbcode" required>{$quote_text}</textarea></td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="forum-form-new-post" value="Post Reply">
			</td>
		</tr>
	</table>
	<input type="hidden" name="record" value="{$record}">
	<input type="hidden" name="nonce" value="{$nonce}">
</form>
