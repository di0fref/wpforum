{if isset($messages)}
	<div id="forum-messages">
		{foreach from=$messages item=message}
			<div class="alert alert-{$message.level}"><i class="fa fa-{$message.level}"></i> &nbsp;{$message.text}</div>
		{/foreach}
	</div>
{/if}