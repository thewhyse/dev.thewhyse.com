<?php
/**
 * The style "default" of the Chat
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

$args = get_query_var('trx_addons_args_sc_chat');

do_action( 'trx_addons_action_sc_chat_before', $args );

?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
	class="sc_chat sc_chat_<?php
		echo esc_attr( $args['type'] );
		if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
	trx_addons_sc_show_attributes( 'sc_chat', $args, 'sc_wrapper' );
	?>><?php

	trx_addons_sc_show_titles('sc_chat', $args);

	do_action( 'trx_addons_action_sc_chat_before_content', $args );

	?><div class="sc_chat_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_chat', $args, 'sc_items_wrapper' ); ?>>
		<div class="sc_chat_form">
			<div class="sc_chat_form_inner">
				<?php
				$trx_addons_ai_helper_prompt_id = 'sc_chat_form_field_prompt_' . mt_rand();
				?>
				<label for="<?php echo esc_attr( $trx_addons_ai_helper_prompt_id ); ?>" class="sc_chat_form_field_prompt_label"><?php esc_attr_e('How can I help you?', 'trx_addons'); ?></label>
				<div class="sc_chat_result">
					<ul class="sc_chat_list"></ul>
				</div>
				<div class="sc_chat_form_field sc_chat_form_field_prompt">
					<div class="sc_chat_form_field_inner">
						<input id="<?php echo esc_attr( $trx_addons_ai_helper_prompt_id ); ?>" type="text" value="<?php echo esc_attr( $args['prompt'] ); ?>" class="sc_chat_form_field_prompt_text" placeholder="<?php esc_attr_e('Type you message ...', 'trx_addons'); ?>">
						<a href="#" class="sc_chat_form_field_prompt_button<?php if ( empty( $args['prompt'] ) ) echo ' sc_chat_form_field_prompt_button_disabled'; ?>"><?php
							if ( ! empty( $args['button_text'] ) ) {
								echo esc_html( $args['button_text'] );
							} else {
								esc_html_e('Send', 'trx_addons');
							}
						?></a>
					</div>
				</div>
			</div>
			<div class="sc_chat_message"></div>
		</div>
	</div>

	<?php
	do_action( 'trx_addons_action_sc_chat_after_content', $args );

	trx_addons_sc_show_links('sc_chat', $args);
	?>

</div><?php

do_action( 'trx_addons_action_sc_chat_after', $args );
