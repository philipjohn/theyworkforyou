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
    $output      = get_transient($cache_key);

    if ( ! $output ) {

        $mp      = $attributes['currentMP'];
        $limit   = isset( $attributes['noOfEntries'] ) ? $attributes['noOfEntries'] : 5;
        $api     = new TWFY_WP_API();
        $activity = $api->get_mp_details_for_activity( [ 'id' => $mp, 'count' => $limit ] );

        ob_start();
        ?>
        <div class="wp-block-theyworkforyou-mps-recent-activity">
            <h2>Recent activity by <?php echo esc_html( $activity['fullName'] ); ?> MP</h2>
            <ul class="mps-activity">
                <?php

                foreach ( $activity['items'] as $item ) {
                    ?>
                    <li class="item">
                        <span class="date">
                            <a href="<?php echo esc_attr( $item['url'] ); ?>">
                                <?php
                                    echo esc_html( $item['date'] );

                                    if ( isset( $item['time'] ) ) {
                                        echo ' at ' . esc_html( $item['time'] );
                                    }
                                ?>
                            </a>
                            in
                            <span class="context"><?php echo esc_html( $item['context'] ); ?></span>
                        </span><br/>
                        <span class="body"><?php echo esc_html( $item['body'] ); ?></span>
                    </li>
                    <?php
                }
            ?>
            </ul>
        </div>
        <?php

        // Cache the output so we can speed up responses in future.
        $output = ob_get_clean();
            set_transient($cache_key, $output, HOUR_IN_SECONDS);
    }

    return $output;
}