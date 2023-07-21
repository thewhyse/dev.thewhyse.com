<?php
global $wpdb;

set_current_screen('edit-docs');
add_filter( 'screen_options_show_screen', '__return_true' );
global $post_type, $post_type_object, $typenow;
$post_type        = 'docs';
$wp_list_table = _get_list_table( 'WP_List_Table' );
$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
$wp_list_table = new BetterDocs_WP_Posts_List_Table();
$pagenum       = $wp_list_table->get_pagenum();
$post_type_object = get_post_type_object( $post_type );
?>
<div class="wrap">
    <hr class="wp-header-end">
    <div class="betterdocs-listing-wrapper">
        <?php do_action('betterdocs_listing_header'); ?>
    </div>
    <div class="tabs-content">
        <div class="betterdocs-tab-content tab-content-1">
            <div class="betterdocs-settings-header betterdocs-settings-filter">
                <div class="betterdocs-header-full">
                    <div class="betterdocs-header-filter">
                        <?php
                        global $wp;
                        $form_action = home_url( $wp->request ) . '/wp-admin/admin.php';
                        ?>
                        <form id="posts-filter" method="get" action="<?php echo $form_action; ?>">
                            <input type="hidden" name="page" class="post_type_page" value="betterdocs-admin" />
                            <input id="post-search-input" class="dashboard-search-field" type="text" placeholder="<?php esc_html_e('Search', 'betterdocs'); ?>" name="s" value="<?php echo (isset($_REQUEST['s'])) ? esc_attr($_REQUEST['s']) : '' ?>">
                            <?php $order = (isset($_GET['order'])) ? $_GET['order'] : ''; ?>
                            <select id="dashboard-select-order" class="dashboard-search-field dashboard-select-order" name="order">
                                <option value=""><?php esc_html_e('Order', 'betterdocs') ?></option>
                                <option value="ASC"<?php echo ('ASC' === $order) ? ' selected' : '' ?>><?php esc_html_e('Ascending', 'betterdocs') ?></option>
                                <option value="DESC"<?php echo ('DESC' === $order) ? ' selected' : '' ?>><?php esc_html_e('Descending', 'betterdocs') ?></option>
                            </select>
                            <?php $date = (isset($_GET['date'])) ? $_GET['date'] : ''; ?>
                            <select id="dashboard-select-date" class="dashboard-search-field dashboard-select-date" name="date">
                                <option value=""><?php esc_html_e('All Dates', 'betterdocs') ?></option>
                                <option value="most_recent"<?php echo ('most_recent' === $date) ? ' selected' : '' ?>><?php esc_html_e('Most Recent', 'betterdocs') ?></option>
                                <option value="least_recent"<?php echo ('least_recent' === $date) ? ' selected' : '' ?>><?php esc_html_e('Least Recent', 'betterdocs') ?></option>
                                <option value="custom_date"<?php echo ('custom_date' === $date) ? ' selected' : '' ?>><?php esc_html_e('Custom', 'betterdocs') ?></option>
                            </select>
                            <input id="reportrange" class="dashboard-select-date-custom" type="text" name="date_range" title="<?php echo (isset($_GET['date_range'])) ? esc_attr($_GET['date_range']) : ''; ?>" value="<?php echo (isset($_GET['date_range'])) ? esc_attr($_GET['date_range']) : ''; ?>" />
                            <select id="dashboard-select-author" class="dashboard-search-field dashboard-select-author" name="author">
                                <option value="all"><?php esc_html_e('All Authors', 'betterdocs') ?></option>
                                <?php
                                $author = (isset($_GET['author'])) ? $_GET['author'] : '';
                                $users = get_users( array( 'role__in' => array( 'administrator', 'editor', 'author', 'contributor' ) ) );
                                foreach ($users as $user) {
                                    ?>
                                    <option value="<?php echo esc_attr($user->data->ID) ?>"<?php echo ($author == $user->data->ID) ? " selected" : "" ?>><?php echo $user->data->display_name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <select id="dashboard-select-doc_category" class="dashboard-search-field dashboard-select-category" name="doc_category">
                                <option value="all"><?php esc_html_e('All Categories', 'betterdocs') ?></option>
                                <?php
                                $selected = (isset($_GET['doc_category'])) ? $_GET['doc_category'] : '';
                                echo BetterDocs_Helper::term_options('doc_category', $selected);
                                ?>
                            </select>
                            <?php do_action('betterdocs_admin_filter_after_category'); ?>
                            <?php $post_status = (isset($_GET['post_status'])) ? $_GET['post_status'] : ''; ?>
                            <select id="dashboard-select-status" class="dashboard-search-field dashboard-select-status" name="post_status">
                                <option value="any"<?php echo ('any' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Doc Status', 'betterdocs') ?></option>
                                <option value="publish"<?php echo ('publish' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Publish', 'betterdocs') ?></option>
                                <option value="pending"<?php echo ('pending' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Pending', 'betterdocs') ?></option>
                                <option value="draft"<?php echo ('draft' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Draft', 'betterdocs') ?></option>
                                <option value="auto-draft"<?php echo ('auto-draft' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Auto Draft', 'betterdocs') ?></option>
                                <option value="future"<?php echo ('future' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Future', 'betterdocs') ?></option>
                                <option value="private"<?php echo ('private' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Private', 'betterdocs') ?></option>
                                <option value="inherit"<?php echo ('inherit' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Inherit', 'betterdocs') ?></option>
                            </select>
                            <?php do_action('betterdocs_admin_filter_before_submit'); ?>
                            <input class="betterdocs-button betterdocs-button-primary betterdocs-search-filter-btn" type="submit" id="search-submit" class="button" value="<?php esc_html_e('Search','betterdocs') ?>">
                        </form>
                    </div>
                </div>
            </div>
            <div class="betterdocs-listing-table-content">
                <?php
                if ( 'post' !== $post_type ) {
                    $parent_file   = "edit.php?post_type=$post_type";
                    $submenu_file  = "edit.php?post_type=$post_type";
                    $post_new_file = "post-new.php?post_type=$post_type";
                } else {
                    $parent_file   = 'edit.php';
                    $submenu_file  = 'edit.php';
                    $post_new_file = 'post-new.php';
                }

                $doaction = $wp_list_table->current_action();

                if ( $doaction ) {
                    check_admin_referer( 'bulk-posts' );

                    $sendback = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'locked', 'ids' ), wp_get_referer() );
                    if ( ! $sendback ) {
                        $sendback = admin_url( $parent_file );
                    }
                    $sendback = add_query_arg( 'paged', $pagenum, $sendback );
                    if ( strpos( $sendback, 'post.php' ) !== false ) {
                        $sendback = admin_url( $post_new_file );
                    }

                    if ( 'delete_all' === $doaction ) {
                        // Prepare for deletion of all posts with a specified post status (i.e. Empty Trash).
                        $post_status = preg_replace( '/[^a-z0-9_-]+/i', '', $_REQUEST['post_status'] );
                        // Validate the post status exists.
                        if ( get_post_status_object( $post_status ) ) {
                            $post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type=%s AND post_status = %s", $post_type, $post_status ) );
                        }
                        $doaction = 'delete';
                    } elseif ( isset( $_REQUEST['media'] ) ) {
                        $post_ids = $_REQUEST['media'];
                    } elseif ( isset( $_REQUEST['ids'] ) ) {
                        $post_ids = explode( ',', $_REQUEST['ids'] );
                    } elseif ( ! empty( $_REQUEST['post'] ) ) {
                        $post_ids = array_map( 'intval', $_REQUEST['post'] );
                    }

                    if ( ! isset( $post_ids ) ) {
                        wp_redirect( $sendback );
                        exit;
                    }

                    switch ( $doaction ) {
                        case 'trash':
                            $trashed = 0;
                            $locked  = 0;

                            foreach ( (array) $post_ids as $post_id ) {
                                if ( ! current_user_can( 'delete_post', $post_id ) ) {
                                    wp_die( __( 'Sorry, you are not allowed to move this item to the Trash.' ) );
                                }

                                if ( wp_check_post_lock( $post_id ) ) {
                                    $locked++;
                                    continue;
                                }

                                if ( ! wp_trash_post( $post_id ) ) {
                                    wp_die( __( 'Error in moving the item to Trash.' ) );
                                }

                                $trashed++;
                            }

                            $sendback = add_query_arg(
                                array(
                                    'trashed' => $trashed,
                                    'ids'     => implode( ',', $post_ids ),
                                    'locked'  => $locked,
                                ),
                                $sendback
                            );
                            break;
                        case 'untrash':
                            $untrashed = 0;

                            if ( isset( $_GET['doaction'] ) && ( 'undo' === $_GET['doaction'] ) ) {
                                add_filter( 'wp_untrash_post_status', 'wp_untrash_post_set_previous_status', 10, 3 );
                            }

                            foreach ( (array) $post_ids as $post_id ) {
                                if ( ! current_user_can( 'delete_post', $post_id ) ) {
                                    wp_die( __( 'Sorry, you are not allowed to restore this item from the Trash.' ) );
                                }

                                if ( ! wp_untrash_post( $post_id ) ) {
                                    wp_die( __( 'Error in restoring the item from Trash.' ) );
                                }

                                $untrashed++;
                            }
                            $sendback = add_query_arg( 'untrashed', $untrashed, $sendback );

                            remove_filter( 'wp_untrash_post_status', 'wp_untrash_post_set_previous_status', 10, 3 );

                            break;
                        case 'delete':
                            $deleted = 0;
                            foreach ( (array) $post_ids as $post_id ) {
                                $post_del = get_post( $post_id );

                                if ( ! current_user_can( 'delete_post', $post_id ) ) {
                                    wp_die( __( 'Sorry, you are not allowed to delete this item.' ) );
                                }

                                if ( 'attachment' === $post_del->post_type ) {
                                    if ( ! wp_delete_attachment( $post_id ) ) {
                                        wp_die( __( 'Error in deleting the attachment.' ) );
                                    }
                                } else {
                                    if ( ! wp_delete_post( $post_id ) ) {
                                        wp_die( __( 'Error in deleting the item.' ) );
                                    }
                                }
                                $deleted++;
                            }
                            $sendback = add_query_arg( 'deleted', $deleted, $sendback );
                            break;
                        case 'edit':
                            if ( isset( $_REQUEST['bulk_edit'] ) ) {
                                $done = bulk_edit_posts( $_REQUEST );

                                if ( is_array( $done ) ) {
                                    $done['updated'] = count( $done['updated'] );
                                    $done['skipped'] = count( $done['skipped'] );
                                    $done['locked']  = count( $done['locked'] );
                                    $sendback        = add_query_arg( $done, $sendback );
                                }
                            }
                            break;
                        default:
                            $screen = get_current_screen()->id;

                            /**
                             * Fires when a custom bulk action should be handled.
                             *
                             * The redirect link should be modified with success or failure feedback
                             * from the action to be used to display feedback to the user.
                             *
                             * The dynamic portion of the hook name, `$screen`, refers to the current screen ID.
                             *
                             * @since 4.7.0
                             *
                             * @param string $sendback The redirect URL.
                             * @param string $doaction The action being taken.
                             * @param array  $items    The items to take the action on. Accepts an array of IDs of posts,
                             *                         comments, terms, links, plugins, attachments, or users.
                             */
                            $sendback = apply_filters( "handle_bulk_actions-{$screen}", $sendback, $doaction, $post_ids ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
                            break;
                    }

                    $sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );

                    wp_redirect( $sendback );
                    exit;
                } elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
                    wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
                    exit;
                }

                $wp_list_table->prepare_items();

                wp_enqueue_script( 'inline-edit-post' );
                wp_enqueue_script( 'heartbeat' );

                if ( 'wp_block' === $post_type ) {
                    wp_enqueue_script( 'wp-list-reusable-blocks' );
                    wp_enqueue_style( 'wp-list-reusable-blocks' );
                }

                $title = $post_type_object->labels->name;

                get_current_screen()->set_screen_reader_content(
                    array(
                        'heading_views'      => $post_type_object->labels->filter_items_list,
                        'heading_pagination' => $post_type_object->labels->items_list_navigation,
                        'heading_list'       => $post_type_object->labels->items_list,
                    )
                );

                add_screen_option(
                    'per_page',
                    array(
                        'default' => 20,
                        'option'  => 'edit_' . $post_type . '_per_page',
                    )
                );

                $bulk_counts = array(
                    'updated'   => isset( $_REQUEST['updated'] ) ? absint( $_REQUEST['updated'] ) : 0,
                    'locked'    => isset( $_REQUEST['locked'] ) ? absint( $_REQUEST['locked'] ) : 0,
                    'deleted'   => isset( $_REQUEST['deleted'] ) ? absint( $_REQUEST['deleted'] ) : 0,
                    'trashed'   => isset( $_REQUEST['trashed'] ) ? absint( $_REQUEST['trashed'] ) : 0,
                    'untrashed' => isset( $_REQUEST['untrashed'] ) ? absint( $_REQUEST['untrashed'] ) : 0,
                );

                $bulk_messages             = array();
                $bulk_messages['post']     = array(
                    /* translators: %s: Number of posts. */
                    'updated'   => _n( '%s post updated.', '%s posts updated.', $bulk_counts['updated'] ),
                    'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 post not updated, somebody is editing it.' ) :
                        /* translators: %s: Number of posts. */
                        _n( '%s post not updated, somebody is editing it.', '%s posts not updated, somebody is editing them.', $bulk_counts['locked'] ),
                    /* translators: %s: Number of posts. */
                    'deleted'   => _n( '%s post permanently deleted.', '%s posts permanently deleted.', $bulk_counts['deleted'] ),
                    /* translators: %s: Number of posts. */
                    'trashed'   => _n( '%s post moved to the Trash.', '%s posts moved to the Trash.', $bulk_counts['trashed'] ),
                    /* translators: %s: Number of posts. */
                    'untrashed' => _n( '%s post restored from the Trash.', '%s posts restored from the Trash.', $bulk_counts['untrashed'] ),
                );
                $bulk_messages['page']     = array(
                    /* translators: %s: Number of pages. */
                    'updated'   => _n( '%s page updated.', '%s pages updated.', $bulk_counts['updated'] ),
                    'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 page not updated, somebody is editing it.' ) :
                        /* translators: %s: Number of pages. */
                        _n( '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.', $bulk_counts['locked'] ),
                    /* translators: %s: Number of pages. */
                    'deleted'   => _n( '%s page permanently deleted.', '%s pages permanently deleted.', $bulk_counts['deleted'] ),
                    /* translators: %s: Number of pages. */
                    'trashed'   => _n( '%s page moved to the Trash.', '%s pages moved to the Trash.', $bulk_counts['trashed'] ),
                    /* translators: %s: Number of pages. */
                    'untrashed' => _n( '%s page restored from the Trash.', '%s pages restored from the Trash.', $bulk_counts['untrashed'] ),
                );
                $bulk_messages['wp_block'] = array(
                    /* translators: %s: Number of blocks. */
                    'updated'   => _n( '%s block updated.', '%s blocks updated.', $bulk_counts['updated'] ),
                    'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 block not updated, somebody is editing it.' ) :
                        /* translators: %s: Number of blocks. */
                        _n( '%s block not updated, somebody is editing it.', '%s blocks not updated, somebody is editing them.', $bulk_counts['locked'] ),
                    /* translators: %s: Number of blocks. */
                    'deleted'   => _n( '%s block permanently deleted.', '%s blocks permanently deleted.', $bulk_counts['deleted'] ),
                    /* translators: %s: Number of blocks. */
                    'trashed'   => _n( '%s block moved to the Trash.', '%s blocks moved to the Trash.', $bulk_counts['trashed'] ),
                    /* translators: %s: Number of blocks. */
                    'untrashed' => _n( '%s block restored from the Trash.', '%s blocks restored from the Trash.', $bulk_counts['untrashed'] ),
                );

                /**
                 * Filters the bulk action updated messages.
                 *
                 * By default, custom post types use the messages for the 'post' post type.
                 *
                 * @since 3.7.0
                 *
                 * @param array[] $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
                 *                               keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
                 * @param int[]   $bulk_counts   Array of item counts for each message, used to build internationalized strings.
                 */
                $bulk_messages = apply_filters( 'bulk_post_updated_messages', $bulk_messages, $bulk_counts );
                $bulk_counts   = array_filter( $bulk_counts );

                ?>
                <div class="wrap">
                    <?php
                    // If we have a bulk message to issue:
                    $messages = array();
                    foreach ( $bulk_counts as $message => $count ) {
                        if ( isset( $bulk_messages[ $post_type ][ $message ] ) ) {
                            $messages[] = sprintf( $bulk_messages[ $post_type ][ $message ], number_format_i18n( $count ) );
                        } elseif ( isset( $bulk_messages['post'][ $message ] ) ) {
                            $messages[] = sprintf( $bulk_messages['post'][ $message ], number_format_i18n( $count ) );
                        }

                        if ( 'trashed' === $message && isset( $_REQUEST['ids'] ) ) {
                            $ids        = preg_replace( '/[^0-9,]/', '', $_REQUEST['ids'] );
                            $messages[] = '<a href="' . esc_url( wp_nonce_url( "admin.php?page=betterdocs-admin&doaction=undo&action=untrash&ids=$ids", 'bulk-posts' ) ) . '">' . __( 'Undo' ) . '</a>';
                        }

                        if ( 'untrashed' === $message && isset( $_REQUEST['ids'] ) ) {
                            $ids = explode( ',', $_REQUEST['ids'] );

                            if ( 1 === count( $ids ) && current_user_can( 'edit_post', $ids[0] ) ) {
                                $messages[] = sprintf(
                                    '<a href="%1$s">%2$s</a>',
                                    esc_url( get_edit_post_link( $ids[0] ) ),
                                    esc_html( get_post_type_object( get_post_type( $ids[0] ) )->labels->edit_item )
                                );
                            }
                        }
                    }

                    if ( $messages ) {
                        echo '<div id="message" class="updated notice is-dismissible"><p>' . implode( ' ', $messages ) . '</p></div>';
                    }
                    unset( $messages );

                    $_SERVER['REQUEST_URI'] = remove_query_arg( array( 'locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed' ), $_SERVER['REQUEST_URI'] );

                    $wp_list_table->display();

                    if ( $wp_list_table->has_items() ) {
                        $wp_list_table->inline_edit();
                    }

                ?>
                <div id="ajax-response"></div>
                <div class="clear" /></div>
            </div>
            </div>
        </div>
        <?php
        global $wpdb;

        $terms_object = array(
            'taxonomy' => 'doc_category',
            'orderby' => 'meta_value_num',
            'meta_key' => 'doc_category_order',
            'order' => 'ASC',
            'hide_empty' => false,
        );

        if ( class_exists('BetterDocs_Multiple_Kb') && BetterDocs_Multiple_Kb::$enable == 1 && isset($_GET['knowledgebase']) && $_GET['knowledgebase'] !== 'all' ) {
            $terms_object['meta_query'] = array(
                array(
                    'key'       => 'doc_category_knowledge_base',
                    'value'     => $_GET['knowledgebase'],
                    'compare'   => 'LIKE'
                )
            );
        }

        $terms = get_terms($terms_object);
        $kb = '';
        if (isset($_GET['knowledgebase']) && !empty($_GET['knowledgebase']) && $_GET['knowledgebase'] !== 'all') {
            $kb = $_GET['knowledgebase'];
        }
        ?>
        <div class="betterdocs-tab-content tab-content-2">
            <div class="betterdocs-listing-content">
                <?php
                $output = '';
                if (is_array($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        $output .= '<div class="betterdocs-single-listing">';
                        $output .= '<div class="betterdocs-single-listing-inner">';
                        $output .= '<h4 class="betterdocs-single-listing-title">' . $term->name . '</h4>';
                        $list_args = BetterDocs_Helper::list_query_arg('docs_any', !empty($kb), $term->slug, '-1', 'betterdocs_order', '', $kb);
                        $args = apply_filters('betterdocs_articles_args', $list_args, $term->term_id);
                        $post_query = new WP_Query($args);

                        if ($post_query->have_posts()) {
                            $output .= '<ul class="docs-droppable" data-category_id="' . $term->term_id . '">';
                            while ($post_query->have_posts()) : $post_query->the_post();
                                $edit_post_link = get_edit_post_link(get_the_ID(), '');
                                $delete_post_link = get_delete_post_link(get_the_ID(), '');
                                $output .= '<li data-id="' . get_the_ID() . '">';
                                $output .= '<div class="betterdocs-single-list-content"><span><svg width="8px" viewBox="0 0 23 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="drag-dots" fill="#C5C5C5" fill-rule="nonzero"><path d="M4,0 C1.79947933,0 0,1.79947933 0,4 C0,6.20052067 1.79947933,8 4,8 C6.20052067,8 8,6.20052067 8,4 C8,1.79947933 6.20052067,0 4,0 Z M4,17 C1.79947933,17 0,18.7994793 0,21 C0,23.2005207 1.79947933,25 4,25 C6.20052067,25 8,23.2005207 8,21 C8,18.7994793 6.20052067,17 4,17 Z M4,34 C1.79947933,34 0,35.7994793 0,38 C0,40.2005207 1.79947933,42 4,42 C6.20052067,42 8,40.2005207 8,38 C8,35.7994793 6.20052067,34 4,34 Z M19,0 C16.7994793,0 15,1.79947933 15,4 C15,6.20052067 16.7994793,8 19,8 C21.2005207,8 23,6.20052067 23,4 C23,1.79947933 21.2005207,0 19,0 Z M19,17 C16.7994793,17 15,18.7994793 15,21 C15,23.2005207 16.7994793,25 19,25 C21.2005207,25 23,23.2005207 23,21 C23,18.7994793 21.2005207,17 19,17 Z M19,34 C16.7994793,34 15,35.7994793 15,38 C15,40.2005207 16.7994793,42 19,42 C21.2005207,42 23,40.2005207 23,38 C23,35.7994793 21.2005207,34 19,34 Z" id="Shape"></path></g></g></svg></span>';
                                $output .= $edit_post_link ? '<a href="post.php?action=edit&post=' . get_the_ID() . '">' : '<span class="betterdocs-article-title">';
                                $output .= wp_kses(get_the_title(), BETTERDOCS_KSES_ALLOWED_HTML);
                                if (get_post_status(get_the_ID()) !== 'publish') {
                                    $output .= ' <span class="betterdocs-draft">'.get_post_status(get_the_ID()).'</span>';
                                }
                                $output .= $edit_post_link ? '</a>' : '</span>';
                                $output .= '<span>';
                                $output .= '<a href="' . get_permalink(get_the_ID(), '') . '" target="_blank" title="View Docs"><span class="dashicons dashicons-external"></span></a>';
                                if ($edit_post_link) {
                                    $output .= '<a href="post.php?action=edit&post=' . get_the_ID() . '" title="Edit Docs"><span class="dashicons dashicons-edit"></span></a>';
                                }
                                if ($delete_post_link) {
                                    $output .= '<a href="' . get_delete_post_link(get_the_ID(), '') . '" title="Delete Docs"><span class="dashicons dashicons-trash"></span></a>';
                                }
                                $output .= '</span></div>';
                                $output .= '</li>';
                            endwhile;
                            $output .= '</ul>';
                        } else {
                            $output .= '<ul class="docs-droppable" data-category_id="' . $term->term_id . '">';
                            $output .= '<li class="betterdocs-no-docs"><svg id="f20e0c25-d928-42cc-98d1-13cc230663ea" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" viewBox="0 0 820.16 780.81"><defs><linearGradient id="07332201-7176-49c2-9908-6dc4a39c4716" x1="539.63" y1="734.6" x2="539.63" y2="151.19" gradientTransform="translate(-3.62 1.57)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="gray" stop-opacity="0.25"/><stop offset="0.54" stop-color="gray" stop-opacity="0.12"/><stop offset="1" stop-color="gray" stop-opacity="0.1"/></linearGradient><linearGradient id="0ee1ab3f-7ba2-4205-9d4a-9606ad702253" x1="540.17" y1="180.2" x2="540.17" y2="130.75" gradientTransform="translate(-63.92 7.85)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/><linearGradient id="abca9755-bed1-4a97-b027-7f02ee3ffa09" x1="540.17" y1="140.86" x2="540.17" y2="82.43" gradientTransform="translate(-84.51 124.6) rotate(-12.11)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/><linearGradient id="2632d424-e666-4ee4-9508-a494957e14ab" x1="476.4" y1="710.53" x2="476.4" y2="127.12" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/><linearGradient id="97571ef7-1c83-4e06-b701-c2e47e77dca3" x1="476.94" y1="156.13" x2="476.94" y2="106.68" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/><linearGradient id="7d32e13e-a0c7-49c4-af0e-066a2f8cb76e" x1="666.86" y1="176.39" x2="666.86" y2="117.95" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/></defs><title>No Docs</title><rect x="317.5" y="142.55" width="437.02" height="603.82" transform="translate(-271.22 62.72) rotate(-12.11)" fill="#e0e0e0"/><g opacity="0.5"><rect x="324.89" y="152.76" width="422.25" height="583.41" transform="translate(-271.22 62.72) rotate(-12.11)" fill="url(#07332201-7176-49c2-9908-6dc4a39c4716)"/></g><rect x="329.81" y="157.1" width="411.5" height="570.52" transform="translate(-270.79 62.58) rotate(-12.11)" fill="#fafafa"/><rect x="374.18" y="138.6" width="204.14" height="49.45" transform="translate(-213.58 43.93) rotate(-12.11)" fill="url(#0ee1ab3f-7ba2-4205-9d4a-9606ad702253)"/><path d="M460.93,91.9c-15.41,3.31-25.16,18.78-21.77,34.55s18.62,25.89,34,22.58,25.16-18.78,21.77-34.55S476.34,88.59,460.93,91.9ZM470.6,137A16.86,16.86,0,1,1,483.16,117,16.66,16.66,0,0,1,470.6,137Z" transform="translate(-189.92 -59.59)" fill="url(#abca9755-bed1-4a97-b027-7f02ee3ffa09)"/><rect x="375.66" y="136.55" width="199.84" height="47.27" transform="translate(-212.94 43.72) rotate(-12.11)" fill="#1fce9c"/><path d="M460.93,91.9a27.93,27.93,0,1,0,33.17,21.45A27.93,27.93,0,0,0,460.93,91.9ZM470.17,135a16.12,16.12,0,1,1,12.38-19.14A16.12,16.12,0,0,1,470.17,135Z" transform="translate(-189.92 -59.59)" fill="#1fce9c"/><rect x="257.89" y="116.91" width="437.02" height="603.82" fill="#e0e0e0"/><g opacity="0.5"><rect x="265.28" y="127.12" width="422.25" height="583.41" fill="url(#2632d424-e666-4ee4-9508-a494957e14ab)"/></g><rect x="270.65" y="131.42" width="411.5" height="570.52" fill="#fff"/><rect x="374.87" y="106.68" width="204.14" height="49.45" fill="url(#97571ef7-1c83-4e06-b701-c2e47e77dca3)"/><path d="M666.86,118c-15.76,0-28.54,13.08-28.54,29.22s12.78,29.22,28.54,29.22,28.54-13.08,28.54-29.22S682.62,118,666.86,118Zm0,46.08a16.86,16.86,0,1,1,16.46-16.86A16.66,16.66,0,0,1,666.86,164Z" transform="translate(-189.92 -59.59)" fill="url(#7d32e13e-a0c7-49c4-af0e-066a2f8cb76e)"/><rect x="377.02" y="104.56" width="199.84" height="47.27" fill="#1fce9c"/><path d="M666.86,118a27.93,27.93,0,1,0,27.93,27.93A27.93,27.93,0,0,0,666.86,118Zm0,44.05A16.12,16.12,0,1,1,683,145.89,16.12,16.12,0,0,1,666.86,162Z" transform="translate(-189.92 -59.59)" fill="#1fce9c"/><g opacity="0.5"><rect x="15.27" y="737.05" width="3.76" height="21.33" fill="#47e6b1"/><rect x="205.19" y="796.65" width="3.76" height="21.33" transform="translate(824.47 540.65) rotate(90)" fill="#47e6b1"/></g><g opacity="0.5"><rect x="451.49" width="3.76" height="21.33" fill="#47e6b1"/><rect x="641.4" y="59.59" width="3.76" height="21.33" transform="translate(523.63 -632.62) rotate(90)" fill="#47e6b1"/></g><path d="M961,832.15a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45,4.61,4.61,0,0,1,5.57-2.57,2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,961,832.15Z" transform="translate(-189.92 -59.59)" fill="#4d8af0" opacity="0.5"/><path d="M326.59,627.09a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45A4.61,4.61,0,0,1,325,631.4a2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,326.59,627.09Z" transform="translate(-189.92 -59.59)" fill="#fdd835" opacity="0.5"/><path d="M855,127.77a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45,4.61,4.61,0,0,1,5.57-2.57,2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,855,127.77Z" transform="translate(-189.92 -59.59)" fill="#fdd835" opacity="0.5"/><circle cx="812.64" cy="314.47" r="7.53" fill="#f55f44" opacity="0.5"/><circle cx="230.73" cy="746.65" r="7.53" fill="#f55f44" opacity="0.5"/><circle cx="735.31" cy="477.23" r="7.53" fill="#f55f44" opacity="0.5"/><circle cx="87.14" cy="96.35" r="7.53" fill="#4d8af0" opacity="0.5"/><circle cx="7.53" cy="301.76" r="7.53" fill="#47e6b1" opacity="0.5"/></svg></li>';
                            $output .= '</ul>';
                        }
                        $output .= '</div>';
                        $output .= '<a href="post-new.php?post_type=docs&cat=' . $term->term_id . '" class="betterdocs-add-new-link"><span class="add-new-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="20px" fill="#b0b2ba"><path d="M 14.970703 2.9726562 A 2.0002 2.0002 0 0 0 13 5 L 13 13 L 5 13 A 2.0002 2.0002 0 1 0 5 17 L 13 17 L 13 25 A 2.0002 2.0002 0 1 0 17 25 L 17 17 L 25 17 A 2.0002 2.0002 0 1 0 25 13 L 17 13 L 17 5 A 2.0002 2.0002 0 0 0 14.970703 2.9726562 z"></path></svg></span><span class="add-new-text">Add New Docs</span></a>';
                        $output .= '</div>';
                    }
                }
                $query = $wpdb->prepare(
                    "SELECT post_title AS title, ID, post_status FROM $wpdb->posts 
                    WHERE post_type = %s 
                    AND ( ( post_status = %s ) OR ( post_status = %s ) OR ( post_status = %s ) OR ( post_status = %s ) ) 
                    AND ID NOT IN  ( SELECT object_id as post_id FROM $wpdb->term_relationships 
                    WHERE  term_taxonomy_id IN ( SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = %s ) )",
                    array(
                        'docs',
                        'publish',
                        'draft',
                        'pending',
                        'private',
                        'doc_category'
                    )
                );
                $uncategorized_docs = $wpdb->get_results($query);
                if (!empty($uncategorized_docs) && is_array($uncategorized_docs)) {
                    $output .= '<div class="betterdocs-single-listing">';
                    $output .= '<div class="betterdocs-single-listing-inner">';
                    $output .= '<h4 class="betterdocs-single-listing-title">' . __('Uncategorized', 'betterdocs') . '</h4>';
                    $output .= '<ul>';
                    foreach ($uncategorized_docs as $doc) {
                        $edit_post_link = get_edit_post_link($doc->ID, '');
                        $delete_post_link = get_delete_post_link($doc->ID, '');
                        $output .= '<li data-id="' . $doc->ID . '">';
                        $output .= '<div class="betterdocs-single-list-content"><span><svg width="8px" viewBox="0 0 23 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="drag-dots" fill="#C5C5C5" fill-rule="nonzero"><path d="M4,0 C1.79947933,0 0,1.79947933 0,4 C0,6.20052067 1.79947933,8 4,8 C6.20052067,8 8,6.20052067 8,4 C8,1.79947933 6.20052067,0 4,0 Z M4,17 C1.79947933,17 0,18.7994793 0,21 C0,23.2005207 1.79947933,25 4,25 C6.20052067,25 8,23.2005207 8,21 C8,18.7994793 6.20052067,17 4,17 Z M4,34 C1.79947933,34 0,35.7994793 0,38 C0,40.2005207 1.79947933,42 4,42 C6.20052067,42 8,40.2005207 8,38 C8,35.7994793 6.20052067,34 4,34 Z M19,0 C16.7994793,0 15,1.79947933 15,4 C15,6.20052067 16.7994793,8 19,8 C21.2005207,8 23,6.20052067 23,4 C23,1.79947933 21.2005207,0 19,0 Z M19,17 C16.7994793,17 15,18.7994793 15,21 C15,23.2005207 16.7994793,25 19,25 C21.2005207,25 23,23.2005207 23,21 C23,18.7994793 21.2005207,17 19,17 Z M19,34 C16.7994793,34 15,35.7994793 15,38 C15,40.2005207 16.7994793,42 19,42 C21.2005207,42 23,40.2005207 23,38 C23,35.7994793 21.2005207,34 19,34 Z" id="Shape"></path></g></g></svg></span>';
                        $output .= '<a href="post.php?action=edit&post=' . $doc->ID . '">';
                        $output .= wp_kses($doc->title, BETTERDOCS_KSES_ALLOWED_HTML);
                        if ($doc->post_status !== 'publish') {
                            $output .= ' <span class="betterdocs-draft">'.$doc->post_status.'</span>';
                        }
                        $output .= '</a>';
                        $output .= '<span>';
                        if ($edit_post_link) {
                            $output .= '<a href="' . $edit_post_link . '"><span class="dashicons dashicons-edit"></span></a>';
                        }
                        $output .= '<a href="post-new.php?post_type=docs"><span class="dashicons dashicons-plus"></span></a>';
                        if ($delete_post_link) {
                            $output .= '<a href="' . $delete_post_link . '"><span class="dashicons dashicons-trash"></span></a>';
                        }
                        $output .= '</span></div>';
                        $output .= '</li>';
                    }
                    $output .= '</ul>';
                    $output .= '</div>';
                    $output .= '<a href="post-new.php?post_type=docs" class="betterdocs-add-new-link"><span class="add-new-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="20px" fill="#b0b2ba"><path d="M 14.970703 2.9726562 A 2.0002 2.0002 0 0 0 13 5 L 13 13 L 5 13 A 2.0002 2.0002 0 1 0 5 17 L 13 17 L 13 25 A 2.0002 2.0002 0 1 0 17 25 L 17 17 L 25 17 A 2.0002 2.0002 0 1 0 25 13 L 17 13 L 17 5 A 2.0002 2.0002 0 0 0 14.970703 2.9726562 z"></path></svg></span><span class="add-new-text">Add New Docs</span></a>';
                    $output .= '</div>';
                }
                if (empty($terms) && empty($uncategorized_docs)) {
                    $output .= '<div class="betterdocs-single-listing">';
                    $output .= '<div class="betterdocs-single-listing-inner">';
                    $output .= '<h4 class="betterdocs-single-listing-title">No Categories Found</h4>';
                    $output .= '<p class="betterdocs-single-listing-sub-title"> Please create a new Category to get started</p>';
                    $output .= '<ul>';
                    $output .= '<li><svg id="f20e0c25-d928-42cc-98d1-13cc230663ea" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" viewBox="0 0 820.16 780.81"><defs><linearGradient id="07332201-7176-49c2-9908-6dc4a39c4716" x1="539.63" y1="734.6" x2="539.63" y2="151.19" gradientTransform="translate(-3.62 1.57)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="gray" stop-opacity="0.25"></stop><stop offset="0.54" stop-color="gray" stop-opacity="0.12"></stop><stop offset="1" stop-color="gray" stop-opacity="0.1"></stop></linearGradient><linearGradient id="0ee1ab3f-7ba2-4205-9d4a-9606ad702253" x1="540.17" y1="180.2" x2="540.17" y2="130.75" gradientTransform="translate(-63.92 7.85)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient><linearGradient id="abca9755-bed1-4a97-b027-7f02ee3ffa09" x1="540.17" y1="140.86" x2="540.17" y2="82.43" gradientTransform="translate(-84.51 124.6) rotate(-12.11)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient><linearGradient id="2632d424-e666-4ee4-9508-a494957e14ab" x1="476.4" y1="710.53" x2="476.4" y2="127.12" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient><linearGradient id="97571ef7-1c83-4e06-b701-c2e47e77dca3" x1="476.94" y1="156.13" x2="476.94" y2="106.68" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient><linearGradient id="7d32e13e-a0c7-49c4-af0e-066a2f8cb76e" x1="666.86" y1="176.39" x2="666.86" y2="117.95" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient></defs><title>No Docs</title><rect x="317.5" y="142.55" width="437.02" height="603.82" transform="translate(-271.22 62.72) rotate(-12.11)" fill="#e0e0e0"></rect><g opacity="0.5"><rect x="324.89" y="152.76" width="422.25" height="583.41" transform="translate(-271.22 62.72) rotate(-12.11)" fill="url(#07332201-7176-49c2-9908-6dc4a39c4716)"></rect></g><rect x="329.81" y="157.1" width="411.5" height="570.52" transform="translate(-270.79 62.58) rotate(-12.11)" fill="#fafafa"></rect><rect x="374.18" y="138.6" width="204.14" height="49.45" transform="translate(-213.58 43.93) rotate(-12.11)" fill="url(#0ee1ab3f-7ba2-4205-9d4a-9606ad702253)"></rect><path d="M460.93,91.9c-15.41,3.31-25.16,18.78-21.77,34.55s18.62,25.89,34,22.58,25.16-18.78,21.77-34.55S476.34,88.59,460.93,91.9ZM470.6,137A16.86,16.86,0,1,1,483.16,117,16.66,16.66,0,0,1,470.6,137Z" transform="translate(-189.92 -59.59)" fill="url(#abca9755-bed1-4a97-b027-7f02ee3ffa09)"></path><rect x="375.66" y="136.55" width="199.84" height="47.27" transform="translate(-212.94 43.72) rotate(-12.11)" fill="#1fce9c"></rect><path d="M460.93,91.9a27.93,27.93,0,1,0,33.17,21.45A27.93,27.93,0,0,0,460.93,91.9ZM470.17,135a16.12,16.12,0,1,1,12.38-19.14A16.12,16.12,0,0,1,470.17,135Z" transform="translate(-189.92 -59.59)" fill="#1fce9c"></path><rect x="257.89" y="116.91" width="437.02" height="603.82" fill="#e0e0e0"></rect><g opacity="0.5"><rect x="265.28" y="127.12" width="422.25" height="583.41" fill="url(#2632d424-e666-4ee4-9508-a494957e14ab)"></rect></g><rect x="270.65" y="131.42" width="411.5" height="570.52" fill="#fff"></rect><rect x="374.87" y="106.68" width="204.14" height="49.45" fill="url(#97571ef7-1c83-4e06-b701-c2e47e77dca3)"></rect><path d="M666.86,118c-15.76,0-28.54,13.08-28.54,29.22s12.78,29.22,28.54,29.22,28.54-13.08,28.54-29.22S682.62,118,666.86,118Zm0,46.08a16.86,16.86,0,1,1,16.46-16.86A16.66,16.66,0,0,1,666.86,164Z" transform="translate(-189.92 -59.59)" fill="url(#7d32e13e-a0c7-49c4-af0e-066a2f8cb76e)"></path><rect x="377.02" y="104.56" width="199.84" height="47.27" fill="#1fce9c"></rect><path d="M666.86,118a27.93,27.93,0,1,0,27.93,27.93A27.93,27.93,0,0,0,666.86,118Zm0,44.05A16.12,16.12,0,1,1,683,145.89,16.12,16.12,0,0,1,666.86,162Z" transform="translate(-189.92 -59.59)" fill="#1fce9c"></path><g opacity="0.5"><rect x="15.27" y="737.05" width="3.76" height="21.33" fill="#47e6b1"></rect><rect x="205.19" y="796.65" width="3.76" height="21.33" transform="translate(824.47 540.65) rotate(90)" fill="#47e6b1"></rect></g><g opacity="0.5"><rect x="451.49" width="3.76" height="21.33" fill="#47e6b1"></rect><rect x="641.4" y="59.59" width="3.76" height="21.33" transform="translate(523.63 -632.62) rotate(90)" fill="#47e6b1"></rect></g><path d="M961,832.15a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45,4.61,4.61,0,0,1,5.57-2.57,2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,961,832.15Z" transform="translate(-189.92 -59.59)" fill="#4d8af0" opacity="0.5"></path><path d="M326.59,627.09a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45A4.61,4.61,0,0,1,325,631.4a2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,326.59,627.09Z" transform="translate(-189.92 -59.59)" fill="#fdd835" opacity="0.5"></path><path d="M855,127.77a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45,4.61,4.61,0,0,1,5.57-2.57,2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,855,127.77Z" transform="translate(-189.92 -59.59)" fill="#fdd835" opacity="0.5"></path><circle cx="812.64" cy="314.47" r="7.53" fill="#f55f44" opacity="0.5"></circle><circle cx="230.73" cy="746.65" r="7.53" fill="#f55f44" opacity="0.5"></circle><circle cx="735.31" cy="477.23" r="7.53" fill="#f55f44" opacity="0.5"></circle><circle cx="87.14" cy="96.35" r="7.53" fill="#4d8af0" opacity="0.5"></circle><circle cx="7.53" cy="301.76" r="7.53" fill="#47e6b1" opacity="0.5"></circle></svg></li>';
                    $output .= '</ui>';

                    $output .= '</div>';
                    $output .= '<a href="edit-tags.php?taxonomy=doc_category&post_type=docs" class="betterdocs-add-new-link"><span class="add-new-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="20px" fill="#b0b2ba"><path d="M 14.970703 2.9726562 A 2.0002 2.0002 0 0 0 13 5 L 13 13 L 5 13 A 2.0002 2.0002 0 1 0 5 17 L 13 17 L 13 25 A 2.0002 2.0002 0 1 0 17 25 L 17 17 L 25 17 A 2.0002 2.0002 0 1 0 25 13 L 17 13 L 17 5 A 2.0002 2.0002 0 0 0 14.970703 2.9726562 z"></path></svg></span><span class="add-new-text">Add New Docs</span></a>';
                    $output .= '</div>';
                }
                echo $output;
                ?>

            </div>
        </div>
    </div>
</div>