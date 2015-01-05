<div class="forum-trail">{$trail}</div>
{include file="wp-content/plugins/wpforum/tpls/message.tpl"}
<h3>Move Topic</h3>
<div class="panel panel-default">
	<div class="panel-heading bold">Move Topic</div>
	<div class="panel-body forum-panel-body">
		<form role="form" name="forum_form" id="forum-form-move-thread" method="post" class="form-horizontal">
			<div class="form-group">
				<label for="forum_id" class="col-sm-2 control-label">Forum</label>

				<div class="col-sm-10">
					<select id="forum_id" name="forum_id" id="forum_id" class="form-control">
						{$forumDD}
					</select>
				</div>
				<input type="hidden" name="nonce" value="{$nonce}">
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary" name="forum-form-move-thread">Submit</button>
				</div>
			</div>
			<input type="hidden" name="thread_id" value="{$thread.id}">
			<input type="hidden" name="original_forum_id" value="{$forum.id}">
		</form>
	</div>
</div>
