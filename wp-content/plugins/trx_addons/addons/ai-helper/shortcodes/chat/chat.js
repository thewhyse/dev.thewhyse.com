/**
 * Shortcode AI Chat
 *
 * @package ThemeREX Addons
 * @since v2.22.0
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

		// Init AI Chat
		container.find( '.sc_chat:not(.sc_chat_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_chat_inited' ),
				$form = $sc.find( '.sc_chat_form' ),
				$prompt = $sc.find( '.sc_chat_form_field_prompt_text' ),
				$button = $sc.find( '.sc_chat_form_field_prompt_button' ),
				$result = $sc.find( '.sc_chat_list' ),
				chat = [];

			// Enable/disable button "Generate"
			$prompt.on( 'change keyup', function(e) {
				$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', $prompt.val() == '' );
			} )
			.trigger( 'change' );

			// Generate answer
			$prompt.on( 'keypress', function(e) {
				if ( e.keyCode == 13 ) {
					e.preventDefault();
					$button.trigger( 'click' );
				}
			} );
			$button.on( 'click', function(e) {
				e.preventDefault();
				var prompt = $prompt.val();

				if ( ! prompt ) {
					return;
				}

				// Add prompt to the chat
				add_to_chat( prompt, 'user' );

				// Display loading animation
				show_loading();

				// Save a number of requests to the client storage
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_chat_count', ++count, expired );

				// Send request via AJAX
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_chat',
					count: count,
					chat: JSON.stringify( chat )
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

					// Hide loading animation
					hide_loading();
					
					// Show result
					$prompt.focus();
					if ( ! rez.error && rez.data.text ) {
						add_to_chat( rez.data.text, 'assistant' );
						$prompt.val( '' ).trigger( 'change' );
					}
					if ( rez.error || rez.data.message ) {
						var msg = rez.error ? rez.error : rez.data.message;
						$form.find( '.sc_chat_message' ).html( msg ).addClass( 'sc_chat_message_show' );
						setTimeout( function() {
							$form.find( '.sc_chat_message' ).removeClass( 'sc_chat_message_show' );
						}, trx_addons_apply_filters( 'trx_addons_filter_sc_chat_message_timeout', 8000 ) );
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

			// Return a layout of the chat list item
			function get_chat_list_item( message, role ) {
				var dt = new Date(),
					hours = dt.getHours(),
					minutes = dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes(),
					am = hours < 12 ? 'AM' : 'PM',
					use_am = trx_addons_apply_filters( 'trx_addons_filter_sc_chat_time_use_am', true ),
					hours = use_am && hours > 12 ? hours - 12 : hours;
				return trx_addons_apply_filters(
					'trx_addons_filter_sc_chat_list_item',
					'<li class="sc_chat_list_item sc_chat_list_item_' + role + '">'
						+ '<span class="sc_chat_list_item_wrap">'
							+ '<span class="sc_chat_list_item_content">' + message + '</span>'
							+ ( role != 'loading' ? '<span class="sc_chat_list_item_time">' + hours + ':' + minutes + ( use_am ? ' ' + am : '' ) + '</span>' : '' )
						+ '</span>'
					+ '</li>',
					message, role
				);
			}

			// Add a new message to the chat and display it
			function add_to_chat( message, role ) {
				// Add to the list of messages
				if ( chat.length === 0 || chat[chat.length-1].role != role || chat[chat.length-1].content != message ) {
					chat.push( {
						'role': role,
						'content': message
					} );
					// Display message
					$result.append( get_chat_list_item( message, role ) );
					// Display chat if it's hidden
					if ( chat.length == 1 ) {
						$result.parent().slideDown( function() {
							scroll_to_bottom();
						});
					} else {
						scroll_to_bottom();
					}
				}
			}

			// Show loading animation
			function show_loading() {
				$form.addClass( 'sc_chat_form_loading' );
				// Add loading animation to the chat
				$result.append( get_chat_list_item( '<span class="sc_chat_list_item_loading_dot"></span><span class="sc_chat_list_item_loading_dot"></span><span class="sc_chat_list_item_loading_dot"></span>', 'loading' ) );
				// Scroll chat to the bottom
				scroll_to_bottom();
			}

			// Hide loading animation
			function hide_loading() {
				$form.removeClass( 'sc_chat_form_loading' );
				$result.find( '.sc_chat_list_item_loading' ).remove();
			}

			// Scroll the chat to the bottom
			function scroll_to_bottom() {
				$result.parent().animate( { scrollTop: $result.parent().prop( 'scrollHeight' ) }, 500 );
			}


		} );

	} );

} );