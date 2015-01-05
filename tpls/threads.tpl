<div class="forum-trail">{$trail}</div>
{include file="wp-content/plugins/wpforum/tpls/message.tpl"}

<div class="row">
	<div class="col-md-6">
		<p class="postpage_title">Forum: {$data.forum.name}</p>
	</div>
	<div class="col-md-6"></div>
</div>

<div class="row marginb">
	<div class="col-md-6">
		{if isset($buttons.buttons)}
			{* Buttons *}
			{foreach from=$buttons.buttons item=button key=name}
				{$button}
			{/foreach}
		{/if}
	</div>
	<div class="col-md-6 forum-menurow">
		{if isset($buttons.tools)}
			{* Tool *}
			<div class="btn-group pull-right tool-menu">
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					Menu <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					{foreach from=$buttons.tools item=button key=name}
						<li>{$button}</li>
					{/foreach}
				</ul>
			</div>
		{/if}
	</div>
</div>
<table border="{$border}" class="forum-table table table-bordered table-striped " cellspacing='0'>
	<tr>
		<th width="60%">Threads</th>
		<th class="align-center">Replies</th>
		<th class="align-center">Views</th>
		<th>Last post by</th>
	</tr>
	{if $data}
		{foreach from=$data.threads item=thread}
			<tr {if $thread.sticky eq 1}class="info"{/if}>
				<td>
					<p class="threadtitle">
						<img width="22" class="forumicon" title="{$thread.icon|ucfirst}" alt="{$thread.icon|ucfirst}" src="{$config.images_dir}/{$thread.icon}.png">
						{if $thread.is_new}<b>{$thread.prefix}<a href="{$thread.href}">{$thread.subject}</a>
							</b>{/if}
						{if $thread.is_new eq 0}{$thread.prefix}<a href="{$thread.href}">{$thread.subject}</a>{/if}
						{if isset($thread.links)}
							{foreach from=$thread.links key=action item=link}
								{$link}
							{/foreach}
						{/if}
					</p>
					<span class="small forumdescription">Started by: {$thread.user->display_name}, {$thread.date|timesince}</span>
				</td>
				<td class="align-center">{$thread.post_replies|number_format:0}</td>
				<td class="align-center">{$thread.views|number_format:0}</td>
				<td>
					{$thread.last_poster.avatar}{$thread.last_poster.display_name}<br>
					<span class="small">{$thread.last_post|timesince}</span>
				</td>
			</tr>
		{/foreach}
	{else}
		<tr>
			<td colspan="5" class="center bold">No topics yet.</td>
		</tr>
	{/if}
</table>
{$pagination}

