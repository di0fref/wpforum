<?php
$fh = new ForumHelper();
$cat = $fh->getCategory($_REQUEST["category"]);
?>
<div id="wrap">
	<h2>Edit Category</h2>
	<form name="edit_category_form" method="post" action="admin-post.php" id="edit_category_form">
		<table class="form-table">
			<tr>
				<td>Name:</td>
				<td>
					<input style="width: 350px;" type="text" name="name" value="<?php echo $cat["name"]?>">
				</td>
			</tr>
			<tr>
				<td>Sort Order:</td>
				<td>
					<input style="width: 350px;" type="text" value="1" name="sort_order" <?php echo $cat["sort_order"]?>
				</td>
			</tr>
			<tr>
				<td>Description</td>
				<td>
					<textarea style="width: 350px; height: 150px;" name="description"><?php echo $cat["description"]?></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input class="button action" type="submit" name="edit_category_submit"></td>
			</tr>
		</table>
		<input type="hidden" name="action" value="wpforum_edit_category">
		<input type="hidden" name="id" value="<?php echo $cat["id"]?>">
	</form>
</div>