<?php
use BetterDocs\WPNotice\Notices;

class BetterDocs_Admin_Notice {
	private static $_instance = null;
	private $insights = null;

	public static function get_instance(){
		if( static::$_instance === null ) {
			static::$_instance = new static;
		}

		return static::$_instance;
	}

	public function __construct(){
		$this->plugin_usage_insights();

		add_action('admin_init', [$this, 'notices']);
	}



	/**
	 * Admin notices for Review and others.
	 *
	 * @return void
	 */
	public function notices(){
		if( ! class_exists('\BetterDocs\WPNotice\Notices') ) {
            require_once BETTERDOCS_DIR_PATH . '/admin/wp-notice/wpnotice.php';
        }

		$notices = new Notices([
            'id'          => 'betterdocs',
            'store'       => 'options',
            'storage_key' => 'notices',
            'version'     => '1.0.0',
            'lifetime'    => 3,
            'styles'      => BETTERDOCS_ADMIN_URL . 'assets/css/notices.css',
        ]);

        /**
         * Review Notice
         * @var mixed $message
         */

        $message = __(
            'We hope you\'re enjoying BetterDocs! Could you please do us a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?',
            'betterdocs'
        );

        $_review_notice = [
            'thumbnail' => BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-icon.svg',
            'html' => '<p>'. $message .'</p>',
            'links' => [
                'later' => array(
                    'link' => 'https://wordpress.org/plugins/betterdocs/#reviews',
                    'target' => '_blank',
                    'label' => __( 'Sure, you deserve it!', 'betterdocs' ),
                    'icon_class' => 'dashicons dashicons-external',
                ),
                'allready' => array(
                    'label' => __( 'I already did', 'betterdocs' ),
                    'icon_class' => 'dashicons dashicons-smiley',
                    'attributes' => [
                        'data-dismiss' => true
                    ],
                ),
                'maybe_later' => array(
                    'label' => __( 'Maybe Later', 'betterdocs' ),
                    'icon_class' => 'dashicons dashicons-calendar-alt',
                    'attributes' => [
                        'data-later' => true,
                        'class' => 'dismiss-btn'
                    ],
                ),
                'support' => array(
                    'link' => 'https://wpdeveloper.com/support',
                    'attributes' => [
                        'target' => '_blank',
                    ],
                    'label' => __( 'I need help', 'betterdocs' ),
                    'icon_class' => 'dashicons dashicons-sos',
                ),
                'never_show_again' => array(
                    'label' => __( 'Never show again', 'betterdocs' ),
                    'icon_class' => 'dashicons dashicons-dismiss',
                    'attributes' => [
                        'data-dismiss' => true
                    ],
                )
            ]
        ];

        $notices->add(
            'review',
            $_review_notice,
            [
                // 'start'       => $notices->time(),
                'start'       => $notices->strtotime('+20 days'),
                'recurrence'  => 30,
                'dismissible' => true,
                // 'refresh'     => BETTERDOCS_VERSION,
                'screens'     => [
                    'dashboard', 'plugins', 'themes', 'edit-page',
                    'edit-post', 'users', 'tools', 'options-general',
                    'nav-menus'
                ]
            ]
        );

        /**
         * Opt-In Notice
         */
        if( $this->insights != null ) {
            $notices->add(
                'opt_in',
                [ $this->insights, 'notice' ],
                [
                    'classes'     => 'updated put-dismiss-notice',
                    'start'       => $notices->time(),
					// 'start'       => $notices->strtotime('+20 days'),
					// 'refresh'     => BETTERDOCS_VERSION,
                    'dismissible' => true,
                    'do_action'   => 'wpdeveloper_notice_clicked_for_betterdocs',
                ]
            );
        }

		$notices->init();
	}

    public function plugin_usage_insights(){
        if( ! class_exists('BetterDocs_Plugin_Usage_Tracker') ) {
            require_once BETTERDOCS_DIR_PATH . '/includes/class-betterdocs-usage-tracker.php';
        }

        $this->insights = BetterDocs_Plugin_Usage_Tracker::get_instance( BETTERDOCS_FILE, [
			'opt_in'       => true,
			'goodbye_form' => true,
			'item_id'      => 'c7b16777b4f1b83f6083'
		] );
		$this->insights->set_notice_options(array(
			'notice' => __('Want to help make <strong>BetterDocs</strong> even more awesome? You can get a <strong>10% discount coupon</strong> for Premium extensions if you allow us to track the usage.', 'betterdocs'),
			'extra_notice' => __('We collect non-sensitive diagnostic data and plugin usage information.
			Your site URL, WordPress & PHP version, plugins & themes and email address to send you the
			discount coupon. This data lets us make sure this plugin always stays compatible with the most
			popular plugins and themes. No spam, I promise.', 'betterdocs'),
		));
		$this->insights->init();
    }
}

BetterDocs_Admin_Notice::get_instance();