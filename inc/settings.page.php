<?php
global $TWFY_Settings; // we'll need this below
?>
<div class="wrap">
    <h2><?php _e('TheyWorkForYou') ?></h2>

    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    	<?php $TWFY_Settings->the_nonce(); ?>
    	<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label for="<?php echo $TWFY_Settings->get_field_name('twfy_api_key'); ?>"><?php _e('API Key') ?></label></th>
					<td>
						<input type="text" id="<?php echo $TWFY_Settings->get_field_name('twfy_api_key'); ?>" name="<?php echo $TWFY_Settings->get_field_name('twfy_api_key'); ?>" value="<?php echo $TWFY_Settings->get_setting('twfy_api_key'); ?>" class="regular-text" /><br />
						<p class="description"><?php _e('Get your free API key at <a href="http://www.theyworkforyou.com/api/key">TheyWorkForYou.com</a>.'); ?></p>
					</td>
				</tr>	
			</tbody>
    	</table>
    	<input class="button-primary" type="submit" value="<?php _e('Save Settings'); ?>" />
    </form>
</div>