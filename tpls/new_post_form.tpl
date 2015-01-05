<div class="forum-trail">{$trail}</div>
{include file="wp-content/plugins/wpforum/tpls/message.tpl"}
<h3>Reply to: {$thread_name}</h3>
<div class="panel panel-default">
	<div class="panel-heading bold">Reply</div>
	<div class="panel-body forum-panel-body">
		<form name="forum_form" id="forum_form_new_post" class="form-horizontal" method="post" role="form">

			<div class="form-group">
				<label for="subject" class="col-sm-2 control-label">Subject</label>

				<div class="col-sm-10">
					<input class="form-control" type="text" id="subject" name="subject" required value="{if isset($quote_data.subject)}{$quote_data.subject}{else}Re: {$thread_name}{/if}">
				</div>
			</div>
			<div class="form-group">
				<label for="text" class="col-sm-2 control-label">Text</label>

				<div class="col-sm-10">
					<div>{$formbuttons}</div>
					<textarea rows="10" class="form-control" name="text" id="text" required>{if isset($quote_data.text)}{$quote_data.text}{/if}</textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button name="forum-form-new-post" type="submit" class="btn btn-primary">Post reply</button>
					<button onclick="goBack()" type="button" class="btn btn-default">Cancel</button>
				</div>
			</div>
			<input type="hidden" name="record" value="{$record}">
			<input type="hidden" name="nonce" value="{$nonce}">
		</form>
	</div>
</div>
