<div class="forum-trail">{$trail}</div>
{if isset($message)}
	<div class="alert alert-warning">{$message}</div>
{/if}
<p class="postpage_title">
	<img width="22" class="forumicon" title="{$data.icon|ucfirst}" alt="{$data.icon|ucfirst}" src="{$config.images_dir}/{$data.icon}.png">
	{$data.prefix}{$data.header}
</p>
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
	{if isset($buttons.buttons)}
		{* Buttons *}
		{foreach from=$buttons.buttons item=button key=name}
			{$button}
		{/foreach}
	{/if}
</div>
<div class="clearfix"></div>
{if $data.posts}
	{foreach from=$data.posts item=post name=posts_array}
		<div class="panel panel-default">
			<div class="panel-heading bold">{$post.date|timesince}
				<span id="post-{$post.id}" class="pull-right small">#{$post.nr}</span></div>
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
					<div class="col-lg-10">
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
<!--
{if $data.posts}
	{foreach from=$data.posts item=post name=posts_array}
		<div class="forum-post-wrapper
			{if $data.solved_post_id eq $post.id}forum-solved-post{/if}{if $post.user_id eq {$data.thread_starter_id}} thread-starter-reply{/if}"
			 id="post_{$post.nr}">
			<div class="bold forum-post-top">{$post.date|timesince}<span class="post-id-meta small">#{$post.nr}</span>
			</div>
			<div class="forum-left">
				<figure class="forum-figure">
					{$post.avatar}
					<figcaption>
						<span class="bold">{if $post.user->display_name eq ""}
							Guest{else}{$post.user->display_name}</span><br><span class="small">Posts: {$post.user->post_count|number_format:0}</span>{/if}
					</figcaption>
				</figure>
			</div>
			<div class="forum-right {if $smarty.foreach.posts_array.first}forum-post-first{/if}">
				<div class="forum-post-meta">
					<p><span class="post-date bold">{$post.subject}</span></p>
				</div>
				<div class="forum-post-text">
					<p>{$post.text}</p>
					{if $post.nr == 1 and isset($data.solved_text)}
						<div class="solved-text">
							<p>
								<span class="solved-title"><b>{$data.solved_title}</b> by {$data.solved_user->display_name}
									, {$data.solved_date|timesince}</span></p>

							<p>{$data.solved_text}</p>
						</div>
					{/if}
				</div>
				{if $post.user->meta.description}
					<div class="forum-post-signature border-top">
						{$post.user->meta.description|nl2br}
					</div>
				{/if}
			</div>
			<div class="forum-post-links">
				<p>
					{foreach from =$post.post_links item=link}
						{$link.link}
					{/foreach}
				</p>
			</div>
		</div>
	{/foreach}
{else}
	<p class="bold center">No posts yet.</p>
{/if}
-->