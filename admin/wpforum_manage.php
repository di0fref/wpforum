<?php
$fh = new ForumHelper();
$cats = $fh->getCategories();

/* Category link */
$add_category_base = 'admin.php?page=wpforum-add-category';
$edit_category_base = 'admin.php?page=wpforum-edit-category';
$delete_category_base = 'admin.php?page=wpforum-delete-category';

$add_category_link = admin_url($add_category_base);
$edit_category_link = admin_url($edit_category_base);
$delete_category_link = admin_url($delete_category_base);

/* Forum links */
$add_forum_base = 'admin.php?page=wpforum-add-forum';
$edit_forum_base = 'admin.php?page=wpforum-edit-forum';
$delete_forum_base = 'admin.php?page=wpforum-delete-forum';

$add_forum_link = admin_url($add_forum_base);
$edit_forum_link = admin_url($edit_forum_base);
$delete_forum_link = admin_url($delete_forum_base);


?>
<div class="wrap">
	<h2>WP-Forum</h2>

	<h3>Manage Categories and Forums</h3>
	<p>
		<a class="add-new-h2" href="<?php echo $add_category_link;?>">Add Category</a>
	</p>
	<?php foreach ($cats as $cat) { ?>
		<table class="widefat">
			<thead>
			<tr>
				<td>Name</td>
				<td></td>
				<td></td>
				<td>Description</td>
				<td>Sort Order</td>
			</tr>
			<tr>
				<th>
					<b>Category: <?php echo $cat["name"]; ?></b>
				</th>
				<th><a href="<?php echo $edit_category_link;?>&category=<?php echo $cat["id"];?>">Edit</a></th>
				<th><a class="admin_delete_category" href='#' data-url="<?php echo $delete_category_link;?>&category=<?php echo $cat["id"];?>">Delete</a></th>

				<th>
					<?php echo $cat["description"]; ?>
				</th>
				<th>
					<?php echo $cat["sort_order"]; ?>
				</th>

			</tr>
			</thead>
			<tbody>
			<tr>
				<td colspan="4"><b>Forums</b></td>
			</tr>
			<?php foreach ($cat["forums"] as $forum) { ?>

				<tr>
					<td> -- <?php echo $forum["name"]; ?></td>
					<td><a href="<?php echo $edit_forum_link;?>&forum=<?php echo $forum["id"];?>">Edit</a></td>
					<td><a class="admin_delete_thread" href="#" data-url="<?php echo $delete_forum_link;?>&forum=<?php echo $forum["id"];?>">Delete</a></td>
					<td><?php echo $forum["description"]; ?></td>
					<td><?php echo $forum["sort_order"]; ?></td>
				</tr>
			<?php } ?>
			<tr>
				<td colspan="4"><a class="add-new-h2" href="<?php echo $add_forum_link;?>&category=<?php echo $cat["id"];?>">Add Forum</a></td>
			</tr>
			</tbody>
		</table>
		<p></p>
	<?php } ?>
</div>