<div class="forum-trail">{$trail}</div>
<h3>Edit Topic</h3>
<div style="width: 500px">
	<form role="form" name="forum-form-edit-thread" id="forum-form-edit-thread" method="post">
		<div class="form-group">
			<label for="subject">Subject</label>
			<input class="form-control" type="text" name="subject" required value="{$thread.subject}">
		</div>
		<div class="checkbox">
			<label>
				<input id="is_question" name="is_question" value="1" {if $thread.is_question eq 1} checked {/if} type="checkbox">
				This is a question
			</label>
		</div>
		<div class="checkbox">
			<label>
				<input id="is_solved" name="is_solved" value="1" {if $thread.is_solved eq 1} checked {/if} type="checkbox">
				Solved
			</label>
		</div>
		{if $user_can_pin}
			<div class="checkbox">
				<label>
					<input id="sticky" name="sticky" value="1" {if $thread.sticky eq 1} checked {/if} type="checkbox">
					Pinned
				</label>
			</div>
		{/if}
		<button type="submit" class="btn btn-default" name="forum-form-edit-thread">Submit</button>
		<input type="hidden" name="thread_id" value="{$thread.id}">
		<input type="hidden" name="nonce" value="{$nonce}">
	</form>
</div>
