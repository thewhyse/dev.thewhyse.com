<div class="betterdocs-settings-right">
    <div class="betterdocs-sidebar">
        <div class="betterdocs-sidebar-block">
            <div class="betterdocs-admin-sidebar-logo">
                <img alt="BetterDocs" src="<?php echo plugins_url( '/', __FILE__ ).'../assets/img/betterdocs-icon.svg'; ?>">
            </div>
            <div class="betterdocs-admin-sidebar-cta">
                <?php     
                    if(class_exists('Betterdocs_Pro')) {
                        wp_sprintf( '<a rel="nofollow" href="%s" target="_blank">%s</a>', 'https://wpdeveloper.com/account', __( 'Manage License', 'betterdocs' ) );
                    } else {
                        wp_sprintf( '<a rel="nofollow" href="%s" target="_blank">%s</a>', 'https://betterdocs.co/upgrade', __( 'Upgrade to Pro', 'betterdocs' ) );
                    }
                ?>
            </div>
        </div>
        <div class="betterdocs-sidebar-block betterdocs-license-block">
            <?php
                if( class_exists( 'Betterdocs_Pro' ) ) {
                    do_action( 'betterdocs_licensing' );
                }
            ?>
        </div>
    </div>
</div>