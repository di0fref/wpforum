<ul class="list-group">
	{foreach from=$tags item=tag}
		<li class="list-group-item small">
			<span class="badge">{$tag.count}</span>
			{$tag.tag_name}
		</li>
	{/foreach}
</ul>