<div class="wrap">
	<h2>WP-Forum</h2>

	<h3>Settings</h3>

	<form method="post" id="wpforum_admin_options_form" action="options.php">
		<?php settings_fields('wpforum-settings-group'); ?>
		<?php do_settings_sections('wpforum-settings-group'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Topics per page</th>
				<td>
					<input class="required" type="text" name="<?php echo AppBase::OPTION_THREADS_VIEW_COUNT; ?>" value="<?php echo get_option(AppBase::OPTION_THREADS_VIEW_COUNT); ?> "/>
					<br>Number of topics to show per page

				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Posts per page</th>
				<td>
					<input type="text" name="<?php echo AppBase::OPTION_POSTS_VIEW_COUNT; ?>" value="<?php echo get_option(AppBase::OPTION_POSTS_VIEW_COUNT); ?>"/>
					<br>Number of posts to show per page
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Throttle posting every</th>
				<td>
					<input type="text" name="<?php echo AppBase::OPTION_POST_THROTTLE_SECONDS; ?>" value="<?php echo get_option(AppBase::OPTION_POST_THROTTLE_SECONDS); ?>"/>
					<br>Set this to the minimum time (in seconds) between posting new replies.
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Display pagination on top</th>
				<td>
					<input type="checkbox" <?php if(get_option(AppBase::OPTION_DISPLAY_PAGINATION_TOP) == 1){?>checked <?php } ?>name="<?php echo AppBase::OPTION_DISPLAY_PAGINATION_TOP; ?>" value="1"/>
					<br>Set this to the show pagination on top as well on the bottom of the forum.
				</td>
			</tr>


			<tr valign="top">
				<th scope="row">Date format.</th>
				<td><input type="text" name="<?php echo AppBase::OPTION_DATE_FORMAT; ?>" value="<?php echo get_option(AppBase::OPTION_DATE_FORMAT); ?>"/>

					<p>Default format: "<?php echo AppBase::OPTION_DEFAULT_DATE_FORMAT; ?>"<br>
						Preview: <?php echo strftime(get_option(AppBase::OPTION_DATE_FORMAT)); ?><br>
						Check <a href='http://php.net/strftime'>http://www.php.net</a> for dateformatting.
					</p>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>
</div>

