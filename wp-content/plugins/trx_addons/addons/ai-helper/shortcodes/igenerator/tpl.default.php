<?php
/**
 * The style "default" of the IGenerator
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

$args = get_query_var('trx_addons_args_sc_igenerator');

$models = TrxAddons\AiHelper\Lists::get_list_ai_image_models();

?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
	class="sc_igenerator sc_igenerator_<?php
		echo esc_attr( $args['type'] );
		if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
	trx_addons_sc_show_attributes( 'sc_igenerator', $args, 'sc_wrapper' );
	?>><?php

	trx_addons_sc_show_titles('sc_igenerator', $args);

	?><div class="sc_igenerator_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_igenerator', $args, 'sc_items_wrapper' ); ?>>
		<div class="sc_igenerator_form<?php
			if ( ! empty( $args['align'] ) && ! trx_addons_is_off( $args['align'] ) ) {
				echo ' sc_igenerator_form_align_' . esc_attr( $args['align'] );
			}
			?>"
			data-igenerator-settings="<?php
				echo esc_attr( trx_addons_encode_settings( array(
					'number' => $args['number'],
					'columns' => $args['columns'],
					'columns_tablet' => $args['columns_tablet'],
					'columns_mobile' => $args['columns_mobile'],
					'size' => $args['size'],
					'demo_thumb_size' => $args['demo_thumb_size'],
					'demo_images' => $args['demo_images'],
					'model' => $args['model'],
				) ) );
		?>">
			<div class="sc_igenerator_form_inner"<?php
				// If a shortcode is called not from Elementor, we need to add the width of the prompt field and alignment
				if ( empty( $args['prompt_width_extra'] ) ) {
					if ( ! empty( $args['prompt_width'] ) && (int)$args['prompt_width'] < 100 ) {
						echo ' style="width:' . esc_attr( $args['prompt_width'] ) . '%"';
					}
				}
			?>>
				<div class="sc_igenerator_form_field sc_igenerator_form_field_prompt<?php
					if ( ! empty( $args['show_settings'] ) && (int) $args['show_settings'] > 0 ) {
						echo ' sc_igenerator_form_field_prompt_with_settings';
					}
				?>">
					<div class="sc_igenerator_form_field_inner">
						<input type="text" value="<?php echo esc_attr( $args['prompt'] ); ?>" class="sc_igenerator_form_field_prompt_text" placeholder="<?php esc_attr_e('Describe what you want or hit a tag below', 'trx_addons'); ?>">
						<a href="#" class="sc_igenerator_form_field_prompt_button<?php if ( empty( $args['prompt'] ) ) echo ' sc_igenerator_form_field_prompt_button_disabled'; ?>"><?php
							if ( ! empty( $args['button_text'] ) ) {
								echo esc_html( $args['button_text'] );
							} else {
								esc_html_e('Generate', 'trx_addons');
							}
						?></a>
					</div>
					<?php if ( ! empty( $args['show_settings'] ) && (int) $args['show_settings'] > 0 ) { ?>
						<a href="#" class="sc_igenerator_form_settings_button trx_addons_icon-sliders"></a>
						<div class="sc_igenerator_form_settings"><?php
							if ( is_array( $models ) ) {
								foreach ( $models as $model => $title ) {
									$id = 'sc_igenerator_form_settings_field_model_' . str_replace( '/', '-', $model );
									?><div class="sc_igenerator_form_settings_field">
										<input type="radio" name="sc_igenerator_form_settings_field_model" value="<?php echo esc_attr( $model ); ?>"<?php
										if ( ! empty( $args['model'] ) && $args['model'] == $model ) {
											echo ' checked="checked"';
										}
									?> id="<?php echo esc_attr( $id ); ?>"><label for="<?php echo esc_attr( $id ); ?>"><?php
										echo esc_html( $title );
									?></label></div><?php
								}
							}
						?></div>
					<?php } ?>
				</div>
				<div class="sc_igenerator_form_field sc_igenerator_form_field_tags"><?php
					if ( ! empty( $args['tags_label'] ) ) {
						?><span class="sc_igenerator_form_field_tags_label"><?php echo esc_html( $args['tags_label'] ); ?></span><?php
					}
					if ( ! empty( $args['tags'] ) && is_array( $args['tags'] ) && count( $args['tags'] ) > 0 ) {
						?><span class="sc_igenerator_form_field_tags_list"><?php
							foreach ( $args['tags'] as $tag ) {
								?><a href="#" class="sc_igenerator_form_field_tags_item" data-tag-prompt="<?php echo esc_attr( $tag['prompt'] ); ?>"><?php echo esc_html( $tag['title'] ); ?></a><?php
							}
						?></span><?php
					}
				?></div>
			</div>
			<div class="trx_addons_loading"></div>
			<div class="sc_igenerator_message"></div>
		</div>
		<div class="sc_igenerator_images sc_igenerator_images_columns_<?php echo esc_attr( $args['columns'] ); ?> sc_igenerator_images_size_<?php echo esc_attr( $args['size'] ); ?>"></div>
	</div>

	<?php trx_addons_sc_show_links('sc_igenerator', $args); ?>

</div>