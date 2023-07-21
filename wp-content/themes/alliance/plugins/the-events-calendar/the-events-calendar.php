<?php
/* Tribe Events Calendar support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'alliance_tribe_events_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'alliance_tribe_events_theme_setup1', 1 );
	function alliance_tribe_events_theme_setup1() {
		add_filter( 'alliance_filter_list_sidebars', 'alliance_tribe_events_list_sidebars' );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'alliance_tribe_events_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'alliance_tribe_events_theme_setup3', 3 );
	function alliance_tribe_events_theme_setup3() {
		if ( alliance_exists_tribe_events() ) {
			// Section 'Tribe Events'
			alliance_storage_merge_array(
				'options', '', array_merge(
					array(
						'events' => array(
							'title' => esc_html__( 'Events', 'alliance' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the events pages', 'alliance' ) ),
							'icon'  => 'icon-events',
							'type'  => 'section',
						),
					),
					alliance_options_get_list_cpt_options( 'events', esc_html__( 'Event', 'alliance' ) )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_tribe_events_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_tribe_events_theme_setup9', 9 );
	function alliance_tribe_events_theme_setup9() {
		if ( alliance_exists_tribe_events() ) {
			add_action( 'wp_enqueue_scripts', 'alliance_tribe_events_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_tribe_events', 'alliance_tribe_events_frontend_scripts', 10, 1 );
			add_action( 'wp_enqueue_scripts', 'alliance_tribe_events_frontend_scripts_responsive', 2000 );
			add_action( 'trx_addons_action_load_scripts_front_tribe_events', 'alliance_tribe_events_frontend_scripts_responsive', 10, 1 );
			add_filter( 'alliance_filter_merge_styles', 'alliance_tribe_events_merge_styles' );
			add_filter( 'alliance_filter_merge_styles_responsive', 'alliance_tribe_events_merge_styles_responsive' );
			add_filter( 'alliance_filter_post_type_taxonomy', 'alliance_tribe_events_post_type_taxonomy', 10, 2 );
			add_filter( 'alliance_filter_detect_blog_mode', 'alliance_tribe_events_detect_blog_mode' );
			add_filter( 'alliance_filter_get_post_categories', 'alliance_tribe_events_get_post_categories', 10, 2 );
			add_filter( 'alliance_filter_get_post_date', 'alliance_tribe_events_get_post_date' );
			add_filter( 'tribe_template_theme_path_list', 'alliance_tribe_events_template_theme_path_list', 10, 3 );
		}
		if ( is_admin() ) {
			add_filter( 'alliance_filter_tgmpa_required_plugins', 'alliance_tribe_events_tgmpa_required_plugins' );
		}

	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'alliance_tribe_events_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('alliance_filter_tgmpa_required_plugins',	'alliance_tribe_events_tgmpa_required_plugins');
	function alliance_tribe_events_tgmpa_required_plugins( $list = array() ) {
		if ( alliance_storage_isset( 'required_plugins', 'the-events-calendar' ) && alliance_storage_get_array( 'required_plugins', 'the-events-calendar', 'install' ) !== false ) {
			$list[] = array(
				'name'     => alliance_storage_get_array( 'required_plugins', 'the-events-calendar', 'title' ),
				'slug'     => 'the-events-calendar',
				'required' => false,
			);
		}
		return $list;
	}
}

if ( ! function_exists( 'alliance_tribe_events_template_theme_path_list' ) ) {
	add_filter( 'tribe_template_theme_path_list', 'alliance_tribe_events_template_theme_path_list', 10, 3 );
	/**
	 * Allows filtering of the list of theme folders in which we will look for the template.
	 *
	 * Hook: add_filter( 'tribe_template_theme_path_list', 'alliance_tribe_events_template_theme_path_list', 10, 3 );
	 *
	 * @param  array   $folders     Complete path to include the base public folder.
	 * @param  string  $namespace   Loads the files from a specified folder from the themes.
	 * @param  self    $template    Current instance of the Tribe__Template.
	 * 
	 * @return array  A filtered array with folders in which we will look for the template.
	 */
	function alliance_tribe_events_template_theme_path_list( $folders, $namespace = false, $template = false ) {
		static $checked = false, $skin_dirs = array();
		// Detect a subfolder inside a theme dir
		$subfolder = ! empty( $folders['parent-theme']['path'] )
						? str_replace( alliance_prepare_path( ALLIANCE_THEME_DIR ), '', alliance_prepare_path( $folders['parent-theme']['path'] ) )
						: '';
		// Add a skin-specific directories
		if ( ! empty( $subfolder ) ) {
			// Check if directories are exists
			if ( ! $checked ) {
				$checked = true;
				$skin_dirs = array_merge(
					ALLIANCE_THEME_DIR == ALLIANCE_CHILD_DIR ? array() : array(
						'child-skin-root'     => trailingslashit( ALLIANCE_CHILD_DIR . alliance_skins_get_current_skin_dir() ) . $subfolder,
						'child-skin-plugins'  => trailingslashit( ALLIANCE_CHILD_DIR . alliance_skins_get_current_skin_dir() ) . 'plugins/the-events-calendar/templates/' . $subfolder,
					),
					array(
						'parent-skin-root'    => trailingslashit( ALLIANCE_THEME_DIR . alliance_skins_get_current_skin_dir() ) . $subfolder,
						'parent-skin-plugins' => trailingslashit( ALLIANCE_THEME_DIR . alliance_skins_get_current_skin_dir() ) . 'plugins/the-events-calendar/templates/' . $subfolder,
					)
				);
				foreach ( $skin_dirs as $key => $dir ) {
					if ( ! is_dir( $dir ) ) {
						unset( $skin_dirs[ $key ] );
					}
				}
			}
			// Add directories to the list of theme-specific folders
			$priority = 5;
			foreach ( $skin_dirs as $key => $dir ) {
				$folders[ $key ] = array(
					'id'       => $key,
					'priority' => $priority++,
					'path'     => $dir,
				);
			}
		}
		// Remove child dir if it is equal to the theme dir
		if (   ! empty( $folders['child-theme']['path'] )
			&& ! empty( $folders['parent-theme']['path'] )
			&& $folders['child-theme']['path'] == $folders['parent-theme']['path']
		) {
			unset( $folders['child-theme'] );
		}
		return $folders;
	}
}

// Remove 'Tribe Events' section from Customizer
if ( ! function_exists( 'alliance_tribe_events_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'alliance_tribe_events_customizer_register_controls', 100 );
	function alliance_tribe_events_customizer_register_controls( $wp_customize ) {
		if ( false ) {
			// Disable Tribe Events Customizer
			$wp_customize->remove_panel( 'tribe_customizer' );
		} else {
			// Leave Tribe Events Customizer enabled and move it down (after WooCommerce settings)
			$sec = $wp_customize->get_panel( 'tribe_customizer' );
			if ( is_object( $sec ) ) {
				$sec->priority = 200;
			}
		}
	}
}


// Check if Tribe Events is installed and activated
if ( ! function_exists( 'alliance_exists_tribe_events' ) ) {
	function alliance_exists_tribe_events() {
		return class_exists( 'Tribe__Events__Main' );
	}
}

// Return true, if current page is any tribe_events page
if ( ! function_exists( 'alliance_is_tribe_events_page' ) ) {
	function alliance_is_tribe_events_page() {
		$rez = false;
		if ( alliance_exists_tribe_events() ) {
			if ( ! is_search() ) {
				$rez = tribe_is_event()
						|| tribe_is_event_query()
						|| tribe_is_event_category()
						|| tribe_is_event_venue()
						|| tribe_is_event_organizer();
			}
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'alliance_tribe_events_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'alliance_filter_detect_blog_mode', 'alliance_tribe_events_detect_blog_mode' );
	function alliance_tribe_events_detect_blog_mode( $mode = '' ) {
		if ( alliance_is_tribe_events_page() ) {
			$mode = 'events';
		}
		return $mode;
	}
}

// Return taxonomy for current post type
if ( ! function_exists( 'alliance_tribe_events_post_type_taxonomy' ) ) {
	//Handler of the add_filter( 'alliance_filter_post_type_taxonomy',	'alliance_tribe_events_post_type_taxonomy', 10, 2 );
	function alliance_tribe_events_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( alliance_exists_tribe_events() && Tribe__Events__Main::POSTTYPE == $post_type ) {
			$tax = Tribe__Events__Main::TAXONOMY;
		}
		return $tax;
	}
}

// Show categories of the current event
if ( ! function_exists( 'alliance_tribe_events_get_post_categories' ) ) {
	//Handler of the add_filter( 'alliance_filter_get_post_categories', 'alliance_tribe_events_get_post_categories', 10, 2 );
	function alliance_tribe_events_get_post_categories( $cats = '', $args = array() ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			$cat_sep = apply_filters(
									'alliance_filter_post_meta_cat_separator',
									'<span class="post_meta_item_cat_separator">' . ( ! isset( $args['cat_sep'] ) || ! empty( $args['cat_sep'] ) ? ', ' : ' ' ) . '</span>',
									$args
									);
			$cats = alliance_get_post_terms( $cat_sep, get_the_ID(), Tribe__Events__Main::TAXONOMY );
		}
		return $cats;
	}
}

// Return date of the current event
if ( ! function_exists( 'alliance_tribe_events_get_post_date' ) ) {
	//Handler of the add_filter( 'alliance_filter_get_post_date', 'alliance_tribe_events_get_post_date');
	function alliance_tribe_events_get_post_date( $dt = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			if ( is_int( $dt ) ) {
				// Return start date and time in the Unix format
				$dt = tribe_get_start_date( get_the_ID(), true, 'U' );
			} else {
				// Return Start Date @ Time - End Date @ Time as a string
				$dt = tribe_events_event_schedule_details( get_the_ID(), '', '' );
				
				// Return Start Date @ Time as a string
				// If second parameter is true - time is showed
				// Example: $dt = tribe_get_start_date( get_the_ID(), true );
			}
		}
		return $dt;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'alliance_tribe_events_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_tribe_events_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_tribe_events', 'alliance_tribe_events_frontend_scripts', 10, 1 );
	function alliance_tribe_events_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && alliance_need_frontend_scripts( 'tribe_events' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$alliance_url = alliance_get_file_url( 'plugins/the-events-calendar/the-events-calendar.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-the-events-calendar', $alliance_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'alliance_tribe_events_frontend_scripts_responsive' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_tribe_events_frontend_scripts_responsive', 2000 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_tribe_events', 'alliance_tribe_events_frontend_scripts_responsive', 10, 1 );
	function alliance_tribe_events_frontend_scripts_responsive( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && alliance_need_frontend_scripts( 'tribe_events' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$alliance_url = alliance_get_file_url( 'plugins/the-events-calendar/the-events-calendar-responsive.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-the-events-calendar-responsive', $alliance_url, array(), null, alliance_media_for_load_css_responsive( 'tribe-events' ) );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'alliance_tribe_events_merge_styles' ) ) {
	//Handler of the add_filter('alliance_filter_merge_styles', 'alliance_tribe_events_merge_styles');
	function alliance_tribe_events_merge_styles( $list ) {
		$list[ 'plugins/the-events-calendar/the-events-calendar.css' ] = false;
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'alliance_tribe_events_merge_styles_responsive' ) ) {
	//Handler of the add_filter('alliance_filter_merge_styles_responsive', 'alliance_tribe_events_merge_styles_responsive');
	function alliance_tribe_events_merge_styles_responsive( $list ) {
		$list[ 'plugins/the-events-calendar/the-events-calendar-responsive.css' ] = false;
		return $list;
	}
}



// Add Tribe Events specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'alliance_tribe_events_list_sidebars' ) ) {
	//Handler of the add_filter( 'alliance_filter_list_sidebars', 'alliance_tribe_events_list_sidebars' );
	function alliance_tribe_events_list_sidebars( $list = array() ) {
		$list['tribe_events_widgets'] = array(
			'name'        => esc_html__( 'Tribe Events Widgets', 'alliance' ),
			'description' => esc_html__( 'Widgets to be shown on the Tribe Events pages', 'alliance' ),
		);
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( alliance_exists_tribe_events() ) {
	$alliance_fdir = alliance_get_file_dir( 'plugins/the-events-calendar/the-events-calendar-style.php' );
	if ( ! empty( $alliance_fdir ) ) {
		require_once $alliance_fdir;
	}
}
