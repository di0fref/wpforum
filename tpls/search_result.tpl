<h3><i class="fa fa-search"></i> &nbsp;Search Results for {$search_frase}</h3>
<p><a href="{$data.link_back}">Advanced search</a></p>
{if isset($data.error)}
	<div class="alert alert-warning"><i class="fa fa-warning"></i> &nbsp;{$data.error}</div>
{else}
	<table border="0" class="forum-table table table-bordered table-striped" cellspacing='0'>

		{if $data.results}
			{foreach from=$data.results item=result}
				<tr>
					<td width="80%">
						<p class="forumtitle">
							<b><a class="lead" href="{$result.link}">{$result.subject}</a></b>
						</p>
						<p>
							{$result.text}
						</p>
						<p><i class="fa fa-tags"></i>
							Tags: {', '|implode:$data.tags}
						</p>
					</td>
					<td class="small">By: {$result.user->display_name}<br>
						{$result.date|date_format}<br><br>

						Views: {$result.meta.views|number_format}<br>
						Replies: {$result.meta.replies|number_format}
					</td>
				</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="2" class="center bold">
					<p>No results met your search criterias.</p>
				</td>
			</tr>
		{/if}
	</table>
{/if}