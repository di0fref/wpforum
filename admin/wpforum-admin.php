<div class="wrap">
	<h2>WP-Forum</h2>

	<h3>Settings</h3>

	<form method="post" id="wpforum_admin_options_form" action="options.php">
		<?php settings_fields('wpforum-settings-group'); ?>
		<?php do_settings_sections('wpforum-settings-group'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Threads per page</th>
				<td>
					<input class="required" type="text" name="wpforum_threads_per_page" value="<?php echo get_option('wpforum_threads_per_page'); ?> "/>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Posts per page</th>
				<td>
					<input type="text" name="wpforum_posts_per_page" value="<?php echo get_option('wpforum_posts_per_page'); ?>"/>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Date format.</th>
				<td><input type="text" name="wpforum_date_format" value="<?php echo get_option('wpforum_date_format'); ?>"/>

					<p>Default format: "F j, Y, H:i".<br>
						Check <a href='http://php.net/manual/en/function.date.php'>http://www.php.net</a> for date
						formatting.</p>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>
</div>

