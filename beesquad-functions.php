<?php
/*
Plugin Name: BeeSquad
Plugin URI: https://www.beesquad.ch/
Description: Add your BeeSquad Widget
Author: FHYVE SàRL
Version: 0.1
*/

if ( ! defined( 'ABSPATH' ) )
	exit;

function beesquad_menu() {
	add_options_page('BeeSquad', 'BeeSquad - Live Chat', 'administrator', 'beesquad-settings', 'beesquad_settings_page', 'dashicons-admin-generic');
}
add_action('admin_menu', 'beesquad_menu');

function beesquad_settings_page() { ?>
<div class="wrap">
<h2>Configure your BeeSquad Widget</h2>
<form method="post" action="options.php">
    <?php settings_fields( 'beesquad-settings' ); ?>
    <?php do_settings_sections( 'beesquad-settings' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">SITE_ID</th>
        <td><input type="text" name="beesquad_tracking_id" value="<?php echo esc_attr( get_option('beesquad_tracking_id') ); ?>" /> <small>Ex. XXXXXXX<br />You can find your SITE_ID in your registration email.</small></td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>
</div>
<?php }

function beesquad_deactivation() {
    delete_option( 'beesquad_tracking_id' );
}
register_deactivation_hook( __FILE__, 'beesquad_deactivation' );

function beesquad_settings() {
	register_setting( 'beesquad-settings', 'beesquad_tracking_id' );
}
add_action( 'admin_init', 'beesquad_settings' );

function beesquad() { ?>
<!-- Script beesquad widget -->
<beesquad-widget site="<?php echo esc_attr( get_option('beesquad_tracking_id') ); ?>"></beesquad-widget>
    <script src="https://sdk.beesquad.ch" type="text/javascript" charset="utf-8" async></script>
<!-- Script beesquad widget -->
<?php
}
add_action( 'wp_footer', 'beesquad', 10 );
