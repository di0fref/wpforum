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
					<input class="required" type="text" name="<?php echo AppBase::OPTION_THREADS_VIEW_COUNT; ?>" value="<?php echo get_option(AppBase::OPTION_THREADS_VIEW_COUNT); ?> "/>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Posts per page</th>
				<td>
					<input type="text" name="<?php echo AppBase::OPTION_POSTS_VIEW_COUNT; ?>" value="<?php echo get_option(AppBase::OPTION_POSTS_VIEW_COUNT); ?>"/>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Date format.</th>
				<td><input type="text" name="<?php echo AppBase::OPTION_DATE_FORMAT; ?>" value="<?php echo get_option(AppBase::OPTION_DATE_FORMAT); ?>"/>

					<p>Default format: <?php echo AppBase::OPTION_DEFAULT_DATE_FORMAT; ?>)<br>
						Preview: <?php echo strftime(AppBase::OPTION_DEFAULT_DATE_FORMAT); ?><br>
						Check <a href='http://php.net/strftime'>http://www.php.net</a> for date
						formatting.</p>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>
</div>

