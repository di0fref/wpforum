<div class="forum-trail">{$trail}</div>
<ul class="nav nav-pills pull-right">
	{foreach from=$buttons item=button key=name}
		<li role="presentation">{$button}</li>
	{/foreach}
</ul>
<div class="clearfix"></div>
<div class="alert alert-danger" role="alert">{$message}</div>