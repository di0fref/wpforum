<div class="forum-trail">{$trail}</div>
{include file="wp-content/plugins/wpforum/tpls/message.tpl"}
<div class="row no-gutters">
	<div class="col-md-6">
		<p class="postpage_title">
			<img width="22" class="forumicon" title="{$data.icon|ucfirst}" alt="{$data.icon|ucfirst}" src="{$config.images_dir}/{$data.icon}.png">
			{$data.prefix}{$data.header}
		</p>
	</div>
	<div class="col-md-6 text-align-right">
		<i class="fa fa-tags"></i>
		Tags: {', '|implode:$data.tags}
	</div>
</div>
<div class="clearfix"></div>
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

{if $data.posts}
	{foreach from=$data.posts item=post name=posts_array}
		<div class="panel panel-default">
			<div class="panel-heading bold">{$post.date|timesince}
				<span id="post-{$post.id}" class="pull-right small">#{$post.nr}</span>
			</div>
			<div class="panel-body forum-panel-body">
				<div class="row forum-post-row">
					<div class="col-md-2 forum-user-meta">
						<div class="thumbnail">
							{$post.avatar}
							<div class="caption align-center">
								{$post.user->display_name}<br>
								Posts: <span class="badge">{$post.user->post_count|number_format:0}</span>
							</div>
						</div>
					</div>
					<div class="col-lg-10 forum-post-text">
						<p class="post-date bold">{$post.subject}</p>
						<hr>
						<p>{$post.text}</p>
						{if $post.nr == 1 and isset($data.solved_text)}
							<div class="alert alert-success" role="alert">
								<p><b>{$data.solved_title}</b> by {$data.solved_user->display_name}
									, {$data.solved_date|timesince}</p>

								<p>{$data.solved_text}</p>
							</div>
						{/if}
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<div class="forum-post-links small">
					{if isset($post.post_links)}
						{foreach from =$post.post_links item=link}
							{$link.link}
						{/foreach}
					{/if}
				</div>
			</div>
		</div>
	{/foreach}
{else}
	<p class="bold center">No posts yet.</p>
{/if}
{$pagination}