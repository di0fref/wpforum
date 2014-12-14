<div class="forum-trail">{$trail}</div>
	<h2 class="postpage_title">
		<img width="22" class="forumicon" title="{$data.icon|ucfirst}" alt="{$data.icon|ucfirst}" src="{$config.images_dir}/{$data.icon}.png">
		{$data.prefix}{$data.header}
	</h2>

<ul class="nav nav-pills pull-right">
	{foreach from=$buttons item=button key=name}
		<li role="presentation">{$button}</li>
	{/foreach}
</ul>

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
					<p><span class=" post-date bold">{$post.subject}</span></p>
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