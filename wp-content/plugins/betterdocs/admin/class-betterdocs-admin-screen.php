<?php
class Betterdocs_Admin_Screen
{
    private $menu_slug;

    public function __construct() {
        $this->menu_slug = 'betterdocs-admin';
        add_filter( 'manage_docs_posts_columns', array( $this, 'set_custom_edit_action_columns' ) );
        add_action( 'manage_docs_posts_custom_column', array( $this, 'manage_custom_columns' ), 10, 2 );
        add_action( 'admin_menu', array( $this, 'menu_page' ) );
        add_filter( 'parent_file', array( $this, 'highlight_admin_menu' ) );

        global $pagenow;
        if ($pagenow == 'edit-tags.php') {
            add_filter( 'submenu_file', array( $this, 'highlight_admin_submenu' ), 10, 2 );
        }
    }

    public function set_custom_edit_action_columns($columns)
    {
        unset( $columns['comments'] );
        $columns['betterdocs_word_count']   = __('Word Count', 'betterdocs');
        return $columns;
    }

    public function manage_custom_columns( $column, $post_id )
    {
		switch ( $column ) {
            case 'betterdocs_word_count':
                $word_count = str_word_count(trim(strip_tags(get_post_field('post_content', $post_id))));
                echo '<span>'. $word_count .'</span>';
				break;
		}
	}

    public function betterdocs_menu_list() {
        $betterdocs_admin_pages = array(
            'betterdocs' => array(
                'page_title' => 'BetterDocs',
                'menu_title' => 'BetterDocs',
                'capability' => 'edit_docs',
                'menu_slug'  => $this->menu_slug,
                'callback'   => $this->betterdocs_admin_output(),
                'icon_url'   =>  BETTERDOCS_ADMIN_URL.'/assets/img/betterdocs-icon-white.svg',
                'position'   => 5
            ),
            'all_docs' => array(
                'parent_slug' =>  $this->menu_slug,
                'page_title'  => __( 'All Docs', 'betterdocs' ),
                'menu_title'  => __( 'All Docs', 'betterdocs'),
                'capability'  => 'edit_docs',
                'menu_slug'   => $this->menu_slug
            ),
            'add_new' => array(
                'parent_slug' =>  $this->menu_slug,
                'page_title'  => __('Add New', 'betterdocs'),
                'menu_title'  => __('Add New', 'betterdocs'),
                'capability'  => 'edit_docs',
                'menu_slug'   => 'post-new.php?post_type=docs'
            ),
            'categories' => array(
                'parent_slug' => $this->menu_slug,
                'page_title'  => __('Categories', 'betterdocs'),
                'menu_title'  => __('Categories', 'betterdocs'),
                'capability'  => 'manage_doc_terms',
                'menu_slug'   => 'edit-tags.php?taxonomy=doc_category&post_type=docs'
            ),
            'tags' => array(
                'parent_slug' => $this->menu_slug,
                'page_title'  => __('Tags', 'betterdocs'),
                'menu_title'  => __('Tags', 'betterdocs'),
                'capability'  => 'manage_doc_terms',
                'menu_slug'   => 'edit-tags.php?taxonomy=doc_tag&post_type=docs'
            ),
            'quick_setup' => array(
                'parent_slug' => $this->menu_slug,
                'page_title'  => __('Quick Setup', 'betterdocs'),
                'menu_title'  => __('Quick Setup', 'betterdocs'),
                'capability'  => 'delete_users',
                'menu_slug'   => 'betterdocs-setup',
                'callback'    => array( new BetterDocsSetupWizard(), 'plugin_setting_page' )
            ),
            'settings' => array(
                'parent_slug' => $this->menu_slug,
                'page_title'  => __('Settings', 'betterdocs'),
                'menu_title'  => __('Settings', 'betterdocs'),
                'capability'  => 'edit_docs_settings',
                'menu_slug'   => 'betterdocs-settings',
                'callback'    => array( new BetterDocs_Settings(), 'settings_page' )
            ),
            'analytics' => array(
                'parent_slug' => $this->menu_slug,
                'page_title'  => __('Analytics', 'betterdocs'),
                'menu_title'  => __('Analytics', 'betterdocs'),
                'text_domain' => 'betterdocs',
                'capability'  => 'read_docs_analytics',
                'menu_slug'   => 'betterdocs-analytics',
                'callback'    => array( new BetterDocs_Settings(), 'analytics_page' )
            ),
            'faq' => array(
                'parent_slug' => $this->menu_slug,
                'page_title'  => __('FAQ Builder', 'betterdocs'),
                'menu_title'  => __('FAQ Builder', 'betterdocs'),
                'text_domain' => 'betterdocs',
                'capability'  => 'read_docs_analytics',
                'menu_slug'   => 'betterdocs-faq',
                'callback'    => array( new BetterDocs_Settings(), 'faq_page' )
            )
        );
        return apply_filters( 'betterdocs_admin_menu' ,$betterdocs_admin_pages );
    }

    /**
     * Admin Menu Page
     *
     * @return void
     */
    public function menu_page()
    {
        foreach( $this->betterdocs_menu_list() as $key => $value ) {
            if( $key === 'betterdocs' ) {
                add_menu_page(
                    $value['page_title'], $value['menu_title'],
                    $value['capability'], $value['menu_slug'], $value['callback'],
                    $value['icon_url'], $value['position']
                );
            } else {
                $parent_slug = isset( $value['parent_slug'] ) ? $value['parent_slug'] : '';
                $page_title  = isset( $value['page_title'] ) ? $value['page_title'] : '';
                $menu_title  = isset( $value['menu_title'] ) ? $value['menu_title'] : '';
                $capability  = isset( $value['capability'] ) ? $value['capability'] : '';
                $menu_slug   = isset( $value['menu_slug'] ) ? $value['menu_slug'] : '';
                $callback    = isset( $value['callback'] ) ? $value['callback'] : '';
                add_submenu_page(
                    $parent_slug,$page_title,
                    $menu_title, $capability,
                    $menu_slug, $callback
                );
            }
        }
    }

    public function highlight_admin_menu($parent_file)
    {
        global $current_screen;

        if( $this->menu_slug === 'betterdocs-admin' && in_array( $current_screen->id, array( 'edit-doc_tag', 'edit-doc_category' ) ) ) {
            $parent_file = 'betterdocs-admin';
        } else {
            if( in_array( $current_screen->id, array( 'edit-doc_tag', 'edit-doc_category' ) ) ) {
                $parent_file = 'edit.php?post_type=docs';
            }
        }
        return apply_filters('betterdocs_highlight_admin_menu', $parent_file);
    }

    public function highlight_admin_submenu($submenu_file)
    {
        global $current_screen, $pagenow;

        if ( $current_screen->post_type == 'docs' ) {
            if ( $pagenow == 'post.php' ) {
                $submenu_file = 'edit.php?post_type=docs';
            }
            if ( $pagenow == 'post-new.php' ) {
                $submenu_file = 'post-new.php?post_type=docs';
            }
            if( $current_screen->id === 'edit-doc_category' ) {
                $submenu_file = 'edit-tags.php?taxonomy=doc_category&post_type=docs';
            }
            if( $current_screen->id === 'edit-doc_tag' ) {
                $submenu_file = 'edit-tags.php?taxonomy=doc_tag&post_type=docs';
            }
            if( $current_screen->id === 'edit-knowledge_base' ) {
                $submenu_file = 'edit-tags.php?taxonomy=knowledge_base&post_type=docs';
            }
        }

        if( 'betterdocs_page_betterdocs-settings' == $current_screen->id ) {
            $submenu_file = 'betterdocs-settings';
        }

        if( 'betterdocs_page_betterdocs-setup' == $current_screen->id ) {
            $submenu_file = 'betterdocs-setup';
        }

        return apply_filters('betterdocs_highlight_admin_submenu', $submenu_file);
    }

    public function header_template()
    { ?>
        <div class="betterdocs-settings-header">
            <div class="betterdocs-header-full">
                <img src="<?php echo BETTERDOCS_ADMIN_URL ?>assets/img/betterdocs-icon.svg" alt="">
                <h2 class="title"><?php esc_html_e('BetterDocs', 'betterdocs') ?></h2>
                <div class="betterdocs-header-button">
                    <a href="edit.php?post_type=docs&bdocs_view=classic" class="betterdocs-button betterdocs-button-secondary"><?php esc_html_e('Switch to Classic UI', 'betterdocs') ?></a>
                    <a href="post-new.php?post_type=docs" class="betterdocs-button betterdocs-button-primary"><?php esc_html_e('Add New Doc', 'betterdocs') ?></a>
                    <?php do_action('betterdocs_admin_screen_after_header_button'); ?>
                </div>
                <div class="betterdocs-list-grid-icon tabs-nav">
                    <a class="icon-wrap icon-wrap-1" href="#" data-toggle-target=".tab-content-1">
                        <svg width="20" height="18" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.449558 4.82253C0.200258 4.82253 0 4.62228 0 4.37298V0.449558C0 0.200258 0.200258 0 0.449558 0H21.0679C21.3418 0 21.5665 0.224779 21.5665 0.498601V4.32393C21.5665 4.59775 21.3418 4.82253 21.0679 4.82253H0.449558Z"/>
                            <path d="M0.449558 12.6694C0.200258 12.6694 0 12.4691 0 12.2198V8.29639C0 8.04709 0.200258 7.84683 0.449558 7.84683H21.0679C21.3418 7.84683 21.5665 8.07161 21.5665 8.34543V12.1708C21.5665 12.4446 21.3418 12.6694 21.0679 12.6694H0.449558Z"/>
                            <path d="M0.449558 20.5162C0.200258 20.5162 0 20.3159 0 20.0666V16.1432C0 15.8939 0.200258 15.6937 0.449558 15.6937H21.0679C21.3418 15.6937 21.5665 15.9184 21.5665 16.1923V20.0176C21.5665 20.2914 21.3418 20.5162 21.0679 20.5162H0.449558Z"/>
                        </svg>
                    </a>
                    <a class="icon-wrap icon-wrap-2" href="#" data-toggle-target=".tab-content-2">
                        <svg width="19" height="19" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.375 0H0.625C0.28125 0 0 0.28125 0 0.625V4.375C0 4.71875 0.28125 5 0.625 5H4.375C4.71875 5 5 4.71875 5 4.375V0.625C5 0.28125 4.71875 0 4.375 0Z"/>
                            <path d="M4.375 7.5H0.625C0.28125 7.5 0 7.78125 0 8.125V11.875C0 12.2188 0.28125 12.5 0.625 12.5H4.375C4.71875 12.5 5 12.2188 5 11.875V8.125C5 7.78125 4.71875 7.5 4.375 7.5Z"/>
                            <path d="M4.375 15H0.625C0.28125 15 0 15.2812 0 15.625V19.375C0 19.7188 0.28125 20 0.625 20H4.375C4.71875 20 5 19.7188 5 19.375V15.625C5 15.2812 4.71875 15 4.375 15Z"/>
                            <path d="M11.875 0H8.125C7.78125 0 7.5 0.28125 7.5 0.625V4.375C7.5 4.71875 7.78125 5 8.125 5H11.875C12.2188 5 12.5 4.71875 12.5 4.375V0.625C12.5 0.28125 12.2188 0 11.875 0Z"/>
                            <path d="M11.875 7.5H8.125C7.78125 7.5 7.5 7.78125 7.5 8.125V11.875C7.5 12.2188 7.78125 12.5 8.125 12.5H11.875C12.2188 12.5 12.5 12.2188 12.5 11.875V8.125C12.5 7.78125 12.2188 7.5 11.875 7.5Z"/>
                            <path d="M11.875 15H8.125C7.78125 15 7.5 15.2812 7.5 15.625V19.375C7.5 19.7188 7.78125 20 8.125 20H11.875C12.2188 20 12.5 19.7188 12.5 19.375V15.625C12.5 15.2812 12.2188 15 11.875 15Z"/>
                            <path d="M19.375 0H15.625C15.2812 0 15 0.28125 15 0.625V4.375C15 4.71875 15.2812 5 15.625 5H19.375C19.7188 5 20 4.71875 20 4.375V0.625C20 0.28125 19.7188 0 19.375 0Z"/>
                            <path d="M19.375 7.5H15.625C15.2812 7.5 15 7.78125 15 8.125V11.875C15 12.2188 15.2812 12.5 15.625 12.5H19.375C19.7188 12.5 20 12.2188 20 11.875V8.125C20 7.78125 19.7188 7.5 19.375 7.5Z"/>
                            <path d="M19.375 15H15.625C15.2812 15 15 15.2812 15 15.625V19.375C15 19.7188 15.2812 20 15.625 20H19.375C19.7188 20 20 19.7188 20 19.375V15.625C20 15.2812 19.7188 15 19.375 15Z"/>
                        </svg>
                    </a>
                </div>
                <div class="betterdocs-switch-mode">
                    <label for="betterdocs-mode-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="dayIcon" x="0px" y="0px" viewBox="0 0 35 35" style="enable-background:new 0 0 35 35;" xml:space="preserve">
							<g id="Sun">
                                <g>
                                    <path style="fill-rule:evenodd;clip-rule:evenodd;" d="M6,17.5C6,16.672,5.328,16,4.5,16h-3C0.672,16,0,16.672,0,17.5    S0.672,19,1.5,19h3C5.328,19,6,18.328,6,17.5z M7.5,26c-0.414,0-0.789,0.168-1.061,0.439l-2,2C4.168,28.711,4,29.086,4,29.5    C4,30.328,4.671,31,5.5,31c0.414,0,0.789-0.168,1.06-0.44l2-2C8.832,28.289,9,27.914,9,27.5C9,26.672,8.329,26,7.5,26z M17.5,6    C18.329,6,19,5.328,19,4.5v-3C19,0.672,18.329,0,17.5,0S16,0.672,16,1.5v3C16,5.328,16.671,6,17.5,6z M27.5,9    c0.414,0,0.789-0.168,1.06-0.439l2-2C30.832,6.289,31,5.914,31,5.5C31,4.672,30.329,4,29.5,4c-0.414,0-0.789,0.168-1.061,0.44    l-2,2C26.168,6.711,26,7.086,26,7.5C26,8.328,26.671,9,27.5,9z M6.439,8.561C6.711,8.832,7.086,9,7.5,9C8.328,9,9,8.328,9,7.5    c0-0.414-0.168-0.789-0.439-1.061l-2-2C6.289,4.168,5.914,4,5.5,4C4.672,4,4,4.672,4,5.5c0,0.414,0.168,0.789,0.439,1.06    L6.439,8.561z M33.5,16h-3c-0.828,0-1.5,0.672-1.5,1.5s0.672,1.5,1.5,1.5h3c0.828,0,1.5-0.672,1.5-1.5S34.328,16,33.5,16z     M28.561,26.439C28.289,26.168,27.914,26,27.5,26c-0.828,0-1.5,0.672-1.5,1.5c0,0.414,0.168,0.789,0.439,1.06l2,2    C28.711,30.832,29.086,31,29.5,31c0.828,0,1.5-0.672,1.5-1.5c0-0.414-0.168-0.789-0.439-1.061L28.561,26.439z M17.5,29    c-0.829,0-1.5,0.672-1.5,1.5v3c0,0.828,0.671,1.5,1.5,1.5s1.5-0.672,1.5-1.5v-3C19,29.672,18.329,29,17.5,29z M17.5,7    C11.71,7,7,11.71,7,17.5S11.71,28,17.5,28S28,23.29,28,17.5S23.29,7,17.5,7z M17.5,25c-4.136,0-7.5-3.364-7.5-7.5    c0-4.136,3.364-7.5,7.5-7.5c4.136,0,7.5,3.364,7.5,7.5C25,21.636,21.636,25,17.5,25z" />
                                </g>
                            </g>
						</svg>
                    </label>
                    <input class="betterdocs-mode-toggle" id="betterdocs-mode-toggle" type="checkbox">
                    <label class="betterdocs-mode-toggle-button" for="betterdocs-mode-toggle"></label>
                    <label for="betterdocs-mode-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="nightIcon" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
							<path d="M96.76,66.458c-0.853-0.852-2.15-1.064-3.23-0.534c-6.063,2.991-12.858,4.571-19.655,4.571  C62.022,70.495,50.88,65.88,42.5,57.5C29.043,44.043,25.658,23.536,34.076,6.47c0.532-1.08,0.318-2.379-0.534-3.23  c-0.851-0.852-2.15-1.064-3.23-0.534c-4.918,2.427-9.375,5.619-13.246,9.491c-9.447,9.447-14.65,22.008-14.65,35.369  c0,13.36,5.203,25.921,14.65,35.368s22.008,14.65,35.368,14.65c13.361,0,25.921-5.203,35.369-14.65  c3.872-3.871,7.064-8.328,9.491-13.246C97.826,68.608,97.611,67.309,96.76,66.458z" />
						</svg>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    public function __header_template()
    {
        $html = '<div class="betterdocs-settings-header">
            <div class="betterdocs-header-full">
                <img src="'.BETTERDOCS_ADMIN_URL.'assets/img/betterdocs-icon.svg" alt="">
                <h2 class="title">'.esc_html__('BetterDocs', 'betterdocs').'</h2>
                <div class="betterdocs-header-button">
                    <a href="edit.php?post_type=docs&bdocs_view=classic" class="betterdocs-button betterdocs-button-secondary">'.esc_html__('Switch to Classic UI', 'betterdocs').'</a>
                    <a href="post-new.php?post_type=docs" class="betterdocs-button betterdocs-button-primary">'.esc_html__('Add New Doc', 'betterdocs').'</a>
                </div>
                <div class="betterdocs-switch-mode">
                    <label for="betterdocs-mode-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="dayIcon" x="0px" y="0px" viewBox="0 0 35 35" style="enable-background:new 0 0 35 35;" xml:space="preserve">
							<g id="Sun">
                                <g>
                                    <path style="fill-rule:evenodd;clip-rule:evenodd;" d="M6,17.5C6,16.672,5.328,16,4.5,16h-3C0.672,16,0,16.672,0,17.5    S0.672,19,1.5,19h3C5.328,19,6,18.328,6,17.5z M7.5,26c-0.414,0-0.789,0.168-1.061,0.439l-2,2C4.168,28.711,4,29.086,4,29.5    C4,30.328,4.671,31,5.5,31c0.414,0,0.789-0.168,1.06-0.44l2-2C8.832,28.289,9,27.914,9,27.5C9,26.672,8.329,26,7.5,26z M17.5,6    C18.329,6,19,5.328,19,4.5v-3C19,0.672,18.329,0,17.5,0S16,0.672,16,1.5v3C16,5.328,16.671,6,17.5,6z M27.5,9    c0.414,0,0.789-0.168,1.06-0.439l2-2C30.832,6.289,31,5.914,31,5.5C31,4.672,30.329,4,29.5,4c-0.414,0-0.789,0.168-1.061,0.44    l-2,2C26.168,6.711,26,7.086,26,7.5C26,8.328,26.671,9,27.5,9z M6.439,8.561C6.711,8.832,7.086,9,7.5,9C8.328,9,9,8.328,9,7.5    c0-0.414-0.168-0.789-0.439-1.061l-2-2C6.289,4.168,5.914,4,5.5,4C4.672,4,4,4.672,4,5.5c0,0.414,0.168,0.789,0.439,1.06    L6.439,8.561z M33.5,16h-3c-0.828,0-1.5,0.672-1.5,1.5s0.672,1.5,1.5,1.5h3c0.828,0,1.5-0.672,1.5-1.5S34.328,16,33.5,16z     M28.561,26.439C28.289,26.168,27.914,26,27.5,26c-0.828,0-1.5,0.672-1.5,1.5c0,0.414,0.168,0.789,0.439,1.06l2,2    C28.711,30.832,29.086,31,29.5,31c0.828,0,1.5-0.672,1.5-1.5c0-0.414-0.168-0.789-0.439-1.061L28.561,26.439z M17.5,29    c-0.829,0-1.5,0.672-1.5,1.5v3c0,0.828,0.671,1.5,1.5,1.5s1.5-0.672,1.5-1.5v-3C19,29.672,18.329,29,17.5,29z M17.5,7    C11.71,7,7,11.71,7,17.5S11.71,28,17.5,28S28,23.29,28,17.5S23.29,7,17.5,7z M17.5,25c-4.136,0-7.5-3.364-7.5-7.5    c0-4.136,3.364-7.5,7.5-7.5c4.136,0,7.5,3.364,7.5,7.5C25,21.636,21.636,25,17.5,25z" />
                                </g>
                            </g>
						</svg>
                    </label>
                    <input class="betterdocs-mode-toggle" id="betterdocs-mode-toggle" type="checkbox">
                    <label class="betterdocs-mode-toggle-button" for="betterdocs-mode-toggle"></label>
                    <label for="betterdocs-mode-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="nightIcon" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
							<path d="M96.76,66.458c-0.853-0.852-2.15-1.064-3.23-0.534c-6.063,2.991-12.858,4.571-19.655,4.571  C62.022,70.495,50.88,65.88,42.5,57.5C29.043,44.043,25.658,23.536,34.076,6.47c0.532-1.08,0.318-2.379-0.534-3.23  c-0.851-0.852-2.15-1.064-3.23-0.534c-4.918,2.427-9.375,5.619-13.246,9.491c-9.447,9.447-14.65,22.008-14.65,35.369  c0,13.36,5.203,25.921,14.65,35.368s22.008,14.65,35.368,14.65c13.361,0,25.921-5.203,35.369-14.65  c3.872-3.871,7.064-8.328,9.491-13.246C97.826,68.608,97.611,67.309,96.76,66.458z" />
						</svg>
                    </label>
                </div>
            </div>
        </div>';

        echo apply_filters('betterdocs_admin_screen_header_nav',$html);
    }

    public function betterdocs_admin_output()
    {
        return array($this, 'betterdocs_admin_display');
    }

    public function betterdocs_admin_display()
    {
        if (file_exists(BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-admin-screen.php')) {
            return include_once BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-admin-screen.php';
        }
    }
}