/**
 * Shortcode IGenerator - Generate images with AI
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready( function() {

	"use strict";

	var $window             = jQuery( window ),
		$document           = jQuery( document ),
		$body               = jQuery( 'body' );

	$document.on( 'action.init_hidden_elements', function(e, container) {

		if ( container === undefined ) {
			container = $body;
		}

		var animation_out = trx_addons_apply_filters( 'trx_addons_filter_sc_igenerator_animation_out', 'fadeOutDownSmall animated normal' ),
			animation_in = trx_addons_apply_filters( 'trx_addons_filter_sc_igenerator_animation_in', 'fadeInUpSmall animated normal' );

		// Init IGenerator
		container.find( '.sc_igenerator:not(.sc_igenerator_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_igenerator_inited' ),
				$form = $sc.find( '.sc_igenerator_form' ),
				$prompt = $sc.find( '.sc_igenerator_form_field_prompt_text' ),
				$button = $sc.find( '.sc_igenerator_form_field_prompt_button' ),
				$settings = $sc.find( '.sc_igenerator_form_settings' ),
				$settings_button = $sc.find( '.sc_igenerator_form_settings_button' ),
				$model = $settings.find( '[name="sc_igenerator_form_settings_field_model"]' ),
				$preview = $sc.find( '.sc_igenerator_images' ),
				fetch_img = '';

			// Show/hide settings popup
			$settings_button.on( 'click', function(e) {
				e.preventDefault();
				$settings.toggleClass( 'sc_igenerator_form_settings_show' );
				return false;
			} );
			// Hide popup on click outside
			$document.on( 'click', function(e) {
				if ( $settings.hasClass( 'sc_igenerator_form_settings_show' ) && ! jQuery( e.target ).closest( '.sc_igenerator_form_settings' ).length ) {
					$settings.removeClass( 'sc_igenerator_form_settings_show' );
				}
			} );
			// Hide popup on a model selected by click (not by arrow keys)
			$model.on( 'click', function(e) {
				setTimeout( function() {
					$settings.removeClass( 'sc_igenerator_form_settings_show' );
				}, 200 );
			} );

			// Change the prompt text on click on the tag
			$sc.find( '.sc_igenerator_form_field_tags_item' ).on( 'click', function(e) {
				e.preventDefault();
				$prompt.val( jQuery( this ).data( 'tag-prompt' ) ).trigger( 'change' );
				return false;
			} );

			// Enable/disable the button on change the prompt text
			$prompt.on( 'change keyup', function(e) {
				$button.toggleClass( 'sc_igenerator_form_field_prompt_button_disabled', $prompt.val() == '' );
			} )
			.trigger( 'change' );

			// Send request via AJAX to generate images
			$button.on( 'click', function(e) {
				e.preventDefault();
				var prompt = $prompt.val(),
					model = $model.filter(':checked').val(),
					settings = $form.data( 'igenerator-settings' );

				if ( ! prompt ) {
					return;
				}

				$form.addClass( 'sc_igenerator_form_loading' );

				// Save a number of requests to the client storage
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_igenerator_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_igenerator_count', ++count, expired );

				// Send request via AJAX
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_igenerator',
					settings: settings,
					prompt: prompt,
					model: model,
					count: count
				}, function( response ) {
					// Prepare response
					var rez = {};
					if ( response == '' || response == 0 ) {
						rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
					} else if ( typeof response == 'string' ) {
						try {
							rez = JSON.parse( response );
						} catch (e) {
							rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
							console.log( response );
						}
					} else {
						rez = response;
					}

					$form.removeClass( 'sc_igenerator_form_loading' );

					// Show images
					if ( ! rez.error ) {
						var images = rez.data.images,
						i = 0;
						// If need to fetch images after timeout
						if ( rez.data.fetch_id ) {
							for ( i = 0; i < rez.data.fetch_number; i++ ) {
								images.push( {
									url: rez.data.fetch_img
								} );
							}
							if ( ! fetch_img ) {
								fetch_img = rez.data.fetch_img;
							}
							var time = rez.data.fetch_time ? rez.data.fetch_time : 2000;
							setTimeout( function() {
								fetchImages( rez.data );
							}, time );
						}
						if ( rez.data.images.length > 0 ) {
							var $images = $preview.find( '.sc_igenerator_image' );
							if ( animation_in || animation_out ) {
								$preview.css( {
									'height': $images.length ? $preview.height() + 'px' : '36vh',
								} );
							}
							if ( ! $images.length ) {
								$preview.show();
							} else if ( animation_out ) {
								$images.removeClass( animation_in ).addClass( animation_out );
							}
							setTimeout( function() {
								var html = '<div class="sc_igenerator_columns_wrap sc_item_columns '
												+ TRX_ADDONS_STORAGE['columns_wrap_class']
												+ ' columns_padding_bottom'
												+ ( rez.data.columns >= rez.data.number ? ' ' + TRX_ADDONS_STORAGE['columns_in_single_row_class'] : '' )
												+ '">';
								for ( var i = 0; i < rez.data.images.length; i++ ) {
									html += '<div class="sc_igenerator_image ' + trx_addons_get_column_class( 1, rez.data.columns, rez.data.columns_tablet, rez.data.columns_mobile )
												+ ( rez.data.fetch_id ? ' sc_igenerator_image_fetch' : '' )
												+ ( animation_in ? ' ' + animation_in : '' )
											+ '">'
												+ '<div class="sc_igenerator_image_inner">'
													+ '<img src="' + rez.data.images[i].url + '" alt=""' + ( rez.data.fetch_id ? ' id="fetch-' + rez.data.fetch_id + '"' : '' ) + '>'
													+ ( rez.data.fetch_id
														? '<span class="sc_igenerator_image_fetch_info">'
																+ '<span class="sc_igenerator_image_fetch_msg">' + rez.data.fetch_msg + '</span>'
																+ '<span class="sc_igenerator_image_fetch_progress">'
																	+ '<span class="sc_igenerator_image_fetch_progressbar"></span>'
																+ '</span>'
															+ '</span>'
														: '' )
												+ '</div>'
											+ '</div>';
								}
								html += '</div>';
								$preview.html( html );
								setTimeout( function() {
									$preview.css( 'height', 'auto' )
								}, animation_in ? 700 : 0 );									
							}, $images.length && animation_out ? 700 : 0 );
						}
						if ( rez.data.message ) {
							$form.find( '.sc_igenerator_message' ).html( rez.data.message ).addClass( 'sc_igenerator_message_show' );
							setTimeout( function() {
								$form.find( '.sc_igenerator_message' ).removeClass( 'sc_igenerator_message_show' );
							}, trx_addons_apply_filters( 'trx_addons_filter_sc_igenerator_message_timeout', 8000 ) );
						}
					} else {
						alert( rez.error );
					}
				} );
			} );

			// Set padding for the prompt field to avoid overlapping the button
			if ( $button.css( 'position' ) == 'absolute' ) {
				var set_prompt_padding = ( function() {
					$prompt.css( 'padding-right', ( Math.ceil( $button.outerWidth() ) + 10 ) + 'px' );
				} )();
				$window.on( 'resize', set_prompt_padding );
			}

			// Fetch images
			function fetchImages(data) {
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_fetch_images',
					fetch_id: data.fetch_id,
					fetch_model: data.fetch_model
				}, function( response ) {
					// Prepare response
					var rez = {};
					if ( response == '' || response == 0 ) {
						rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
					} else if ( typeof response == 'string' ) {
						try {
							rez = JSON.parse( response );
						} catch (e) {
							rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
							console.log( response );
						}
					} else {
						rez = response;
					}
					if ( ! rez.error ) {
						if ( rez.data && rez.data.images && rez.data.images.length > 0 ) {
							var images = rez.data.images,
								$fetch = $preview.find( 'img#fetch-' + data.fetch_id );
							// Fade out fetch placeholders
							if ( animation_out ) {
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i ).parents( '.sc_igenerator_image_fetch' )
										.removeClass( animation_in )
										.addClass( animation_out );
								}
							}
							// Replace fetch placeholders with real images
							setTimeout( function() {
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i ).attr( 'src', images[i].url );
								}
							}, animation_out ? 300 : 0 );
							// Fade in real images
							setTimeout( function() {
								for ( var i = 0; i < images.length; i++ ) {
									$fetch.eq( i )
										.parents( '.sc_igenerator_image_fetch' )
											.removeClass( 'sc_igenerator_image_fetch' )
											.find( '.sc_igenerator_image_fetch_info')
												.remove();
									if ( animation_in ) {
										trx_addons_when_images_loaded( $fetch.eq( i ).parents( '.sc_igenerator_image' ), function( $img ) {
											$img
												.removeClass( animation_out )
												.addClass( animation_in );
										} );
									}
								}
							}, animation_out ? 800 : 0 );
						} else {
							setTimeout( function() {
								fetchImages( data );
							}, data.fetch_time ? data.fetch_time : 2000 );
						}
					} else {
						alert( rez.error );
					}
				} );
			}

		} );

	} );

} );