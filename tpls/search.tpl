<h3>Search Forums</h3>
<div class="panel panel-default">
	<div class="panel-heading bold">Search</div>
	<div class="panel-body forum-panel-body">
		<form role="form" name="forum_form" id="forum-search" class="form-horizontal" method="post">
			<div class="form-group">
				<label for="search_term" class="col-sm-2 control-label">Search Term</label>

				<div class="col-sm-10">
					<input class="form-control" id="search_term" type="text" name="search_term" required>
				</div>
			</div>

			<div class="form-group">
				<label for="search_criteria" class="col-sm-2 control-label">Search in</label>
				<div class="col-sm-10">
					<select name="search_criteria" id="search_criteria" class="form-control">
						<option value="titles">Search Titles Only</option>
						<option value="posts">Search Entire Posts</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="search_between" class="col-sm-2 control-label">Posted Between</label>
				<div class="col-sm-2">
					<input name="search_start_date" class="form-control" type="date">
				</div>
				<div class="col-sm-2">
					<input name="search_end_date" class="form-control" type="date">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary" name="forum-search">Search</button>
					<button onclick="goBack()" type="button" class="btn btn-default">Cancel</button>
				</div>
			</div>
			<input type="hidden" name="nonce" value="{$nonce}">
		</form>
	</div>
</div>