<div class="forum-trail">{$trail}</div>
{if isset($message)}
	<div class="alert alert-warning">{$message}</div>
{/if}
<div class="menu-row">
	{* Tools *}
	{if isset($buttons.tools)}
		<div class="btn-group pull-right">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				Topic tools <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				{foreach from=$buttons.tools item=button key=name}
					<li>{$button}</li>
				{/foreach}
			</ul>
		</div>
	{/if}
	{* Buttons *}
	{if isset($buttons.buttons)}
		{foreach from=$buttons.buttons item=button key=name}
			{$button}
		{/foreach}
	{/if}
</div>
<div class="clearfix"></div>
{foreach from=$data item=cat}
	<table border="{$border}" class="forum-table table table-bordered table-striped" cellspacing='0'>
		<tr>
			<th width="60%">{$cat.name}</th>
			<th class="align-center">Topics</th>
			<th class="align-center">Posts</th>
			<th>Last Post</th>
		</tr>
		{foreach from=$cat.forums item=forum}
			<tr class="{cycle values="odd,even"}">
				<td>
					<p class="forumtitle">
						<img width="22" class="forumicon" title="{$thread.icon|ucfirst}" alt="{$thread.icon|ucfirst}" src="{$config.images_dir}/category.png">
						<a href="{$forum.href}">{$forum.name}</a>
						{$forum.links.rss}
					</p>

					<span class="forumdescription small">{$forum.description}</span>
				</td>
				<td class="align-center">{$forum.thread_count|number_format:0}</td>
				<td class="align-center">{$forum.post_count|number_format:0}</td>
				<td>{$forum.last_post|timesince}</td>
			</tr>
		{/foreach}
	</table>
{/foreach}