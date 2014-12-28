<div class="forum-trail">{$trail}</div>
{if isset($message)}
	<div class="alert alert-warning">{$message}</div>
{/if}
<div class="menu-row">
	{if isset($buttons.tools)}
		{* Tool *}
		<div class="btn-group pull-right">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				Forum tools <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				{foreach from=$buttons.tools item=button key=name}
					<li>{$button}</li>
				{/foreach}
			</ul>
		</div>
	{/if}
	{if isset($buttons.buttons)}
		{* Buttons *}
		{foreach from=$buttons.buttons item=button key=name}
			{$button}
		{/foreach}
	{/if}
</div>
<div class="clearfix"></div>
<table border="{$border}" class="forum-table table table-bordered table-striped " cellspacing='0'>
	<tr>
		<th width="60%">Threads</th>
		<th class="align-center">Replies</th>
		<th class="align-center">Views</th>
		<th>Last post by</th>
	</tr>
	{if $data}
		{foreach from=$data item=thread}
			<tr>
				<td>
					<p class="threadtitle">
						<img width="22" class="forumicon" title="{$thread.icon|ucfirst}" alt="{$thread.icon|ucfirst}" src="{$config.images_dir}/{$thread.icon}.png">
						{$thread.prefix}<a href="{$thread.href}">{$thread.subject}</a>
						{if isset($thread.links)}
							{foreach from=$thread.links key=action item=link}
								{$link}
							{/foreach}
						{/if}
					</p>
					<span class="small forumdescription">Started by: {$thread.user->display_name}
						, {$thread.date|timesince}</span>
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
			<td colspan="5" class="center bold">No threads yet.</td>
		</tr>
	{/if}
</table>

