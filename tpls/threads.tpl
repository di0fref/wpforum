<div class="forum-trail">{$trail}</div>
<ul class="nav nav-pills pull-right">
	{foreach from=$buttons item=button key=name}
		<li role="presentation">{$button}</li>
	{/foreach}
</ul>


<table border="{$border}" class="{$forum_table_class}" cellspacing='0'>
	<tr>
		<th width="60%">Threads</th>
		<th>Posts</th>
		<th>Views</th>
		<th>Last post by</th>
	</tr>
	{if $data}
		{foreach from=$data item=thread}
			<tr class="{cycle values="odd,even"}">
				<td>
					<h2 class="threadtitle">
						<img width="22" class="forumicon" title="{$thread.icon|ucfirst}" alt="{$thread.icon|ucfirst}" src="{$config.images_dir}/{$thread.icon}.png">
						{$thread.prefix}<a href="{$thread.href}">{$thread.subject}</a>
					</h2>
					<span class="forum-small forumdescription">Started by: {$thread.user->display_name}
						, {$thread.date|date_format:$config.date_format}</span>
				</td>
				<td>{$thread.post_replies|number_format:0}</td>
				<td>{$thread.views|number_format:0}</td>
				<td>
					{if isset($thread.last_poster.display_name)}{$thread.last_poster.display_name}{/if}<br>
					<span class="forum-small">{$thread.last_post|timesince}</span>
				</td>
			</tr>
		{/foreach}
	{else}
		<tr>
			<td colspan="5" class="center bold">No threads yet.</td>
		</tr>
	{/if}
</table>

