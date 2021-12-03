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

    // Use cached output if we have it.
    $cache_key   = md5( json_encode( $attributes ) );
    $cache_group = 'theyworkforyou';
    $output      = wp_cache_get( $cache_key, $cache_group );

    if ( ! $output ) {

        $mp      = $attributes['currentMP'];
        $limit   = isset( $attributes['noOfEntries'] ) ? $attributes['noOfEntries'] : 5;
        $api     = new TWFY_WP_API();
        $mp_info = $api->get_mp_by_person_id( $mp );
        $hansard = $api->get_hansard_by_person_id( $mp, [ 'limit' => $limit ] );

        ob_start();
        ?>
        <div class="wp-block-theyworkforyou-mps-recent-activity">
            <h2>Recent activity by <?php echo esc_html( $mp_info[0]->full_name ); ?> MP</h2>
            <ul class="mps-activity">
                <?php

                foreach ( $hansard->rows as $item ) {
                    ?>
                    <li class="item">
                        <span class="date">
                            <a href="https://theyworkforyou.com<?php echo esc_attr( $item->listurl ); ?>">
                                <?php
                                    echo date( 'D, jS F Y', strtotime( $item->hdate ) );

                                    if ( isset( $item->htime ) ) {
                                        echo ' at ' . date( 'g:ia', strtotime( $item->htime ) );
                                    }
                                ?>
                            </a>
                            in
                            <span class="context"><?php echo esc_html( $item->parent->body ); ?></span>
                        </span><br>
                        <span class="body"><?php echo esc_html( strip_tags( $item->extract ) ); ?></span>
                    </li>
                    <?php
                }
            ?>
            </ul>
        </div>
        <?php

        // Cache the output so we can speed up responses in future.
        $output = ob_get_clean();
        wp_cache_set( $cache_key, $output, $cache_group, HOUR_IN_SECONDS );
    }

    return $output;
}