/* global jQuery:false */
/* global ALLIANCE_STORAGE:false */

(function(blocks, i18n, element) {
	"use strict";

	trx_addons_add_filter('trx_addons_gb_map_get_params', function(array, elem) {
		/* Calendar */
		if ( elem == 'trx-addons/calendar' ) {
			array = trx_addons_object_merge(
				{
					image: {
						type: 'number',
						default: 0
					},
					image_url: {
						type: 'string',
						default: ''
					}
				},
				array
			);
		}
		return array;
	});

	trx_addons_add_filter('trx_addons_gb_map_add_params', function(array, elem, props) {
		/* Title buttons */
		if ( elem == 'common/button' ) {
			array = alliance_gb_map_remove_param(array, 'link_image');
		}

		/* Calendar */
		if ( elem == 'trx-addons/calendar' ) {
			array.push({
							'name': 'image',
							'name_url': 'image_url',
							'title': i18n.__( 'Image source URL:' ),
							'type': 'image'
						});
		}

		/* Slider */
		if ( elem == 'trx-addons/slider' ) {			
			array = alliance_gb_map_remove_param(array, 'large');
			
			array = alliance_gb_map_remove_param(array, 'controller');
			array = alliance_gb_map_remove_param(array, 'controller_style');
			array = alliance_gb_map_remove_param(array, 'controller_pos');
			array = alliance_gb_map_remove_param(array, 'controller_controls');
			array = alliance_gb_map_remove_param(array, 'controller_effect');
			array = alliance_gb_map_remove_param(array, 'controller_per_view');
			array = alliance_gb_map_remove_param(array, 'controller_space');
			array = alliance_gb_map_remove_param(array, 'controller_height');
		}

		/* Blogger */
		if ( elem == 'trx-addons/blogger-details' ) {
			array = alliance_gb_map_remove_param(array, 'image_position');
			array = alliance_gb_map_remove_param(array, 'full_post');
			array = alliance_gb_map_remove_param(array, 'on_plate');
			array = alliance_gb_map_remove_param(array, 'numbers');

			array = alliance_gb_map_remove_param(array, 'image_ratio', {
				'type': [ 'default' ],
				'template_default': [ 'classic', 'over' ],
			});
			array = alliance_gb_map_remove_param(array, 'thumb_size', {
				'type': [ '^news', '^list' ],
				'template_default': [ '^modern' ]
			});
			array = alliance_gb_map_remove_param(array, 'hover', {
				'type': [ '^list' ],
				'template_default': [ '^modern' ]
			});
			array = alliance_gb_map_remove_param(array, 'hide_excerpt', {
				'type': [ '^list' ]
			});
			array = alliance_gb_map_remove_param(array, 'excerpt_length', {
				'type': [ '^list' ],
				'hide_excerpt': [ false ]
			});
			array = alliance_gb_map_remove_param(array, 'more_text', {
				'type': [ '^list' ],
				'more_button': [ true ],
				'no_links': [ false ]
			});			
		}
		return array;
	});

	trx_addons_add_filter('trx_addons_gb_map_add_params', function(array, elem, props) {
		/* Filters params */
		if ( elem == 'common/filters' ) {
			array = [];
		}
		return array;
	});

	function alliance_gb_map_remove_param(array, param) {
		for( var i = 0; i < array.length; i++ ) {
			if ( array[i]['name'] == param ) {
				array.splice(i, 1); 
			}
		}
		return array;
	}

	function alliance_gb_map_update_dependency(array, param, dependency) {
		for( var i = 0; i < array.length; i++ ) {
			if ( array[i]['name'] == param ) {
				array[i]['dependency'] = dependency;
			}
		}
		return array;
	}

})( window.wp.blocks, window.wp.i18n, window.wp.element );