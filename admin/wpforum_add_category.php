<div id="wrap">
	<h2>Add New Category</h2>
	<form name="add_category_form" method="post" action="admin-post.php" id="add_category_form">
		<table class="form-table">
			<tr>
				<td>Name:</td>
				<td>
					<input style="width: 350px;" type="text" name="name">
				</td>
			</tr>
			<tr>
				<td>Sort Order:</td>
				<td>
					<input style="width: 350px;" type="text" value="1" name="sort_order">
				</td>
			</tr>
			<tr>
				<td>Description</td>
				<td>
					<textarea style="width: 350px; height: 150px;" name="description"></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input class="button action" type="submit" name="add_category_submit"></td>
			</tr>
		</table>
		<input type="hidden" name="action" value="wpforum_add_category">
	</form>
</div>