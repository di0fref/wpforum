<div class="forum-trail">{$trail}</div>
{include file="wp-content/plugins/wpforum/tpls/message.tpl"}
<h3>Editing: {$post.subject}</h3>

<div class="panel panel-default">
	<div class="panel-heading bold">Edit post</div>
	<div class="panel-body forum-panel-body">
		<form name="forum_form" id="forum-form-edit-post" method="post" class="form-horizontal">
			<div class="form-group">
				<label for="subject" class="col-sm-2 control-label">Subject</label>

				<div class="col-sm-10">
					<input class="form-control" type="text" name="subject" id="subject" required value="{$post.subject}">
				</div>
			</div>
			<div class="form-group">
				<label for="text" class="col-sm-2 control-label">Text</label>
				<div class="col-sm-10">
					<div>{$formbuttons}</div>
					<textarea rows="10" class="form-control" name="text" id="text" required>{$post.text}</textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button name="forum-form-edit-post" type="submit" class="btn btn-primary">Update post</button>
					<button onclick="goBack()" type="button" class="btn btn-default">Cancel</button>
				</div>
			</div>

			<input type="hidden" name="record" value="{$post.id}">
			<input type="hidden" name="thread_id" value="{$post.parent_id}">
			<input type="hidden" name="nonce" value="{$nonce}">
		</form>
	</div>
</div>