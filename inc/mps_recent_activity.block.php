<?php
/**
 * MPs Recent Activity block
 *
 * Contains the registration hook to create the MPs Recent Activity block.
 *
 * @since 0.4.0
 *
 * @package TheyWorkForYou
 * @subpackage Blocks
 */
namespace TheyWorkForYou;
use TheyWorkForYou\TWFY_WP_API;

function twfy_register_block_type() {
    register_block_type(
        TWFY_PLUGIN_DIR,
        [
            'render_callback' => __NAMESPACE__ . '\twfy_render_callback'
        ]
    );
}
add_action( 'init', __NAMESPACE__ . '\twfy_register_block_type' );

function twfy_render_callback( $attributes ) {
    $mp = $attributes['currentMP'];
    $api = new TWFY_WP_API();
    $mp_info = $api->get_mp_by_person_id( $mp );
    $hansard = $api->get_hansard_by_person_id( $mp );

    ob_start();
    ?>
    <h2>Recent activity by <?php echo esc_html( $mp_info[0]->full_name ); ?> MP</h2>
    <ul>
        <?php

        foreach ( $hansard->rows as $item ) {
            ?>
            <li>
                <!-- <span class="date"><?php echo date( 'd/m/Y H:i:s', $item->hdate ); ?></span> -->
                <span class="body"><?php echo esc_html( strip_tags( $item->body ) ); ?></span>
            </li>
            <?php
        }
    ?>
    </ul>
    <?php

    return ob_get_clean();
}