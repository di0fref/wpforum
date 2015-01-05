<div class="forum-trail">{$trail}</div>
{include file="wp-content/plugins/wpforum/tpls/message.tpl"}
<h3>Start New Topic</h3>

<div class="panel panel-default">
	<div class="panel-heading bold">New Topic</div>
	<div class="panel-body forum-panel-body">
		<form role="form" name="forum_form" id="forum-form-new-thread" class="form-horizontal" method="post">
			<div class="form-group">
				<label for="subject" class="col-sm-2 control-label">Subject</label>

				<div class="col-sm-10">
					<input class="form-control" type="text" id="subject" name="subject" required>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<div class="checkbox">
						<label>
							<input name="is_question" value="1" type="checkbox"> This is a question
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="text" class="col-sm-2 control-label">Text</label>
				<div class="col-sm-10">
					<div>{$formbuttons}</div>
					<textarea rows="10" class="form-control" name="text" id="text" required></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="tags" class="col-sm-2 control-label">Tags</label>
				<div class="col-sm-10">
					<input class="form-control" type="text" name="tags" id="tags" data-role="tagsinput">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary" name="forum-form-new-thread">Submit Topic</button>
					<button onclick="goBack()" type="button" class="btn btn-default">Cancel</button>
				</div>
			</div>
			<input type="hidden" name="record" value="{$record}">
			<input type="hidden" name="nonce" value="{$nonce}">
		</form>
	</div>
</div>
