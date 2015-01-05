<div class="forum-trail">{$trail}</div>
{include file="wp-content/plugins/wpforum/tpls/message.tpl"}
<h3>Edit Topic</h3>
<div class="panel panel-default">
	<div class="panel-heading bold">Edit Topic</div>
	<div class="panel-body forum-panel-body">
		<form role="form" name="forum_form" id="forum-form-edit-thread" method="post" class="form-horizontal">
			<div class="form-group">
				<label for="subject" class="col-sm-2 control-label">Subject</label>

				<div class="col-sm-10">
					<input class="form-control" type="text" name="subject" required value="{$thread.subject}">
				</div>
			</div>
			{if $user_can_pin}
				<div class="form-group">
					<label for="status" class="col-sm-2 control-label">Status</label>

					<div class="col-sm-10">
						<select id="status" name="status" id="status" class="form-control">
							{$statusDD}
						</select>
					</div>
				</div>
			{/if}
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<div class="checkbox">
						<label>
							<input id="is_question" name="is_question" value="1" {if $thread.is_question eq 1} checked {/if} type="checkbox">This is a question
						</label>
					</div>
				</div>
				<div class="col-sm-offset-2 col-sm-10">
					<div class="checkbox">
						<label>
							<input id="is_solved" name="is_solved" value="1" {if $thread.is_solved eq 1} checked {/if} type="checkbox">Solved
						</label>
					</div>
				</div>

				{if $user_can_pin}
					<div class="col-sm-offset-2 col-sm-10">
						<div class="checkbox">
							<label>
								<input id="sticky" name="sticky" value="1" {if $thread.sticky eq 1} checked {/if} type="checkbox">Pinned
							</label>
						</div>
					</div>
				{/if}
			</div>
			<div class="form-group">
				<label for="tags" class="col-sm-2 control-label">Tags</label>
				<div class="col-sm-10">
					<input value="{$tags}" class="form-control" type="text" name="tags" id="tags" data-role="tagsinput">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary" name="forum-form-edit-thread">Submit</button>
					<button onclick="goBack()" type="button" class="btn btn-default">Cancel</button>
				</div>
			</div>
			<input type="hidden" name="thread_id" value="{$thread.id}">
			<input type="hidden" name="nonce" value="{$nonce}">
		</form>
	</div>
</div>