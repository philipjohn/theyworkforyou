<?php
/**
 * Add the submenu page.
 */
function twfy_options_page() {
    add_submenu_page(
        'options-general.php',
        'TheyWorkForYou',
        'TheyWorkForYou',
        'manage_options',
        'twfy',
        'twfy_options_page_html'
    );
}
add_action( 'admin_menu', 'twfy_options_page' );
 
 
/**
 * Register our sections and fields.
 */
function twfy_settings_init() {

    // Register a new setting for "twfy" page.
    register_setting( 'twfy', 'twfy_settings' );
 
    // Register a new section in the "twfy" page.
    add_settings_section(
        'twfy_section_connection',
        esc_html__( 'Connection Settings', 'theyworkforyou' ),
        '__return_false',
        'twfy'
    );
 
    // Register a new field in the "twfy_section_connection" section, inside the "twfy" page.
    add_settings_field(
        'twfy_api_key',
        esc_html__( 'API Key', 'twfy' ),
        'twfy_api_key_cb',
        'twfy',
        'twfy_section_connection',
        array(
            'label_for' => 'twfy_api_key',
        )
    );
}
add_action( 'admin_init', 'twfy_settings_init' );
 
/**
 * twfy_api_key field callback function.
 *
 * @param array $args Arguments passed from the $args array in `add_settings_field()`
 */
function twfy_api_key_cb( $args ) {
    // Get the value of the setting we've registered with register_setting()
    $options = get_option( 'twfy_settings' );
    ?>
    <input
        type="text"
        name="twfy_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
        value="<?php echo esc_attr( $options['twfy_api_key'] ); ?>" />
    <?php
}

/**
 * Top level menu callback function
 */
function twfy_options_page_html() {
    
    // Check this user has adequate privileges.
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
 
    // Show any error/update messages.
    settings_errors( 'twfy_messages' );
    ?>
    <div class="wrap">

        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <form action="options.php" method="post">
            <?php
        
            // Output security fields for the registered setting "twfy".
            settings_fields( 'twfy' );
        
            // Output setting sections and their fields.
            do_settings_sections( 'twfy' );
        
            // Output save settings button.
            submit_button( 'Save Settings' );

            ?>
        </form>
    </div>
    <?php
}