<?php
$fh = new ForumHelper();
$forum =  $fh->getForum($_REQUEST["forum"]);
$cat_dd = $fh->getCatDD($forum["parent_id"]);
?>
<div id="wrap">
	<h2>Edit Forum</h2>
	<form name="add_forum_form" method="post" action="admin-post.php" id="add_forum_form">
		<table class="form-table">
			<tr>
				<td>Name:</td>
				<td>
					<input style="width: 350px;" type="text" name="name" value="<?php echo $forum["name"];?>">
				</td>
			</tr>
			<tr>
				<td>Sort Order:</td>
				<td>
					<input style="width: 350px;" type="text" name="sort_order" value="<?php echo $forum["sort_order"];?>">
				</td>
			</tr>
			<tr>
				<td>Category:</td>
				<td><?php echo $cat_dd ?></td>
			</tr>
			<tr>
				<td>Description</td>
				<td>
					<textarea style="width: 350px; height: 150px;" name="description"><?php echo $forum["description"];?></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input class="button action" type="submit" name="edit_forum_submit"></td>
			</tr>
		</table>
		<input type="hidden" name="action" value="wpforum_edit_forum">
		<input type="hidden" name="forum" value="<?php echo $_REQUEST["forum"];?>">
	</form>
</div>