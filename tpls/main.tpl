<div class="forum-trail">{$trail}</div>

<ul class="nav nav-pills pull-right">
	{foreach from=$buttons item=button key=name}
		<li>{$button}</li>
	{/foreach}
</ul>

{foreach from=$data item=cat}
	<table border="{$border}" class="forum-table" cellspacing='0'>
		<tr>
			<th width="60%">{$cat.name}</th>
			<th>Threads</th>
			<th>Posts</th>
			<th>Last Post</th>
		</tr>
		{foreach from=$cat.forums item=forum}
			<tr class="{cycle values="odd,even"}">
				<td>
					<h2 class="forumtitle">
						<img width="22" class="forumicon" title="{$thread.icon|ucfirst}" alt="{$thread.icon|ucfirst}" src="{$config.images_dir}/category.png">
						<a href="{$forum.href}">{$forum.name}</a></h2>
					<span class="forumdescription forum-small">{$forum.description}</span>
				</td>
				<td>{$forum.thread_count|number_format:0}</td>
				<td>{$forum.post_count|number_format:0}</td>
				<td>{$forum.last_post|timesince}</td>
			</tr>
		{/foreach}
	</table>
{/foreach}