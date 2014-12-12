<div class="forum-trail">{$trail}</div>
<div class="forum-title" id="left">{$data.prefix}{$data.header}</div>
<div class="forum-buttons" id="right">
	<ul>
		{foreach from=$buttons item=button key=name}
			<li>{$button}</li>
		{/foreach}
	</ul>
</div>

{if $data.posts}
	{foreach from=$data.posts item=post name=posts_array}
		<div class="forum-post-wrapper
			{if $data.solved_post_id eq $post.id}forum-solved-post{/if}{if $post.user_id eq {$data.thread_starter_id}} thread-starter-reply{/if}"
			 id="post_{$post.nr}">
			<div class="bold forum-post-top"><!--Posted: -->{$post.date|timesince}<span
						class="post-id-meta small">#{$post.nr}</span>
				{if $data.solved_post_id eq $post.id}<span class="solved-post-message bold">("Question is solved by this post)</span>{/if}
			</div>
			<div class="forum-left">
				<figure class="forum-figure">
					{$post.avatar}
					<figcaption><span class="bold">{if $post.user->display_name eq ""}
							Guest{else}{$post.user->display_name}</span><br><span
								class="small">Posts: {$post.user->post_count|number_format:0}</span>{/if}
					</figcaption>
				</figure>
			</div>
			<div class="forum-right {if $smarty.foreach.posts_array.first}forum-post-first{/if}">
				<div class="forum-post-meta">
					<!--<span class="post-author bold">{if $post.user->display_name eq ""}Guest{else}{$post.user->display_name}{/if}</span><br>-->
					<span class="small post-date bold">Re: {$data.header}</span>
					<!--<span class="post-id-meta">#{$post.nr}</span>-->
				</div>
				<div class="forum-post-text">
					{$post.text}
					{if $post.nr == 1 and $data.solved_text}
						<div class="solved-text">
							<span class="solved-title"><b>{$data.solved_title}</b> by {$data.solved_user->display_name}, {$data.solved_date|timesince}</span><br>
							{$data.solved_text}
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
				<!--<ul>
					{foreach from =$post.post_links item=link key=name}
						<li class="small">
							{if $link.text}
								<a href="{$link.href}">{$link.text}</a>
							{else}
								{$link.href}
							{/if}
						</li>
					{/foreach}
				</ul>-->
				{foreach from =$post.post_links item=link name=links}
					{if $link.text}
						<a href="{$link.href}">{$link.text}</a>
					{else}
						{$link.href}
					{/if}
					{if $smarty.foreach.links.last != true} | {/if}
				{/foreach}
			</div>
		</div>
	{/foreach}
{else}
	<p class="bold center">No posts yet.</p>
{/if}
