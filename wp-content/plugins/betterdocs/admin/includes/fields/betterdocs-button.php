<?php

    if( isset( $field['disable'] ) && $field['disable'] === true ) {
        $attrs .= ' disabled';
    }
    
?>

<input type="button" class="<?php echo esc_attr( $class ); ?>" id="<?php echo $field_id; ?>" name="<?php echo esc_attr($name); ?>" data-nonce="<?php echo wp_create_nonce('betterdocs_'. $name .'_nonce'); ?>" data-key="<?php echo $name; ?>" value="<?php echo esc_html($field['value']) ?>" <?php echo esc_attr($attrs); ?> />