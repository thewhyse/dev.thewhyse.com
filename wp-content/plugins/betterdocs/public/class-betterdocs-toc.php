<?php

class Node {
    public $title;
    public $tag;
    public $tag_number;
    public $key;
    
    public function print( $items, $toc_hierarchy, $level ){
        $html = '';
        if( ! empty ( $items ) ) {
            $toc_hierarchy_class  = ( $toc_hierarchy != 'off' &&  $toc_hierarchy != '' ) ? 'toc-list betterdocs-hierarchial-toc' : 'toc-list';
            $html                .= $level === 1 ? '<ul class = "'.$toc_hierarchy_class.'">' :  '<ul class = "betterdocs-toc-list-level-'.$level.'">';
            foreach( $items as $item ) {
                $html .= '<li class = "betterdocs-toc-heading-level-'.($item->key).'">';
                    $html .= '<a href="#'.$item->tag_number.'">'.strip_tags( $item->title ).'</a>';
                    if( ! empty ( $items ) ) {
                        $html .= $this->print( $item->items, $toc_hierarchy, ++$level );
                    } else {
                        $level = 1;
                        break;
                    }
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }
}


class Betterdocs_TOC{

    public function __construct() {
        add_shortcode( 'betterdocs_toc', array( $this, 'betterdocs_toc' ) );
    }

    public function betterdocs_toc( $atts )
    {
        do_action( 'betterdocs_before_shortcode_load' );

        $get_args = shortcode_atts(
            array(
                'post_type'             => 'docs',
                'post_id'               => get_the_ID(),
                'htags'                 => '1,2,3,4,5,6',
                'hierarchy'             => '',
                'list_number'           => '',
                'collapsible_on_mobile' => '',
                'toc_title'             => '',
            ),
            $atts
        );

        $get_post     = get_post( $get_args['post_id'] );
        $post_content = $get_post->post_content;

        return self::betterdocs_toc_content(
            $post_content,
            $get_args['htags'],
            $get_args['hierarchy'],
            $get_args['list_number'],
            $get_args['collapsible_on_mobile'],
            $get_args['toc_title']
        );
    }

    public static function betterdocs_toc_content( $content, $htags, $toc_hierarchy, $list_number, $collapsible, $toc_title='' ) {
		$html     = '';
        $toc_data = self::format_toc_data( $content, $htags, $toc_hierarchy );

		if ( ! empty( $toc_data->items ) ) {
			$toc_class = array( 'betterdocs-toc' );
            if ( empty( $toc_title ) ) {
                $toc_title = BetterDocs_DB::get_settings('toc_title') ? BetterDocs_DB::get_settings('toc_title') : esc_html__( 'Table of Contents', 'betterdocs' );
				$toc_title = stripslashes($toc_title);
            }

            if ( $collapsible == '1' ) {
				$toc_class[] = 'collapsible-sm';
				$collapsible_arrow = "<svg class='angle-icon angle-up' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='angle-up' class='svg-inline--fa fa-angle-up fa-w-10' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path fill='currentColor' d='M177 159.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 255.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 329.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1z'></path></svg><svg class='angle-icon angle-down' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='angle-down' class='svg-inline--fa fa-angle-down fa-w-10' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path fill='currentColor' d='M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z'></path></svg>";
			} else {
				$collapsible_arrow = null;
			}

			if ( $list_number == '1' ) {
				$toc_class[] = 'toc-list-number';
			}

			$html  = '<div class="' . implode( ' ', $toc_class ) . '">';
			$html .= '<span class="toc-title">' . $toc_title . $collapsible_arrow . '</span>';
			$html .=  $toc_data->print( $toc_data->items, $toc_hierarchy, 1 );
			$html .='</div>';
		}

		return $html;
	}

    /**
     * This method is responsible for re-arranging the TOC data based on hierarchy or non-hierarchy
     *
     * @param string $post_content
     * @param string $htag_support
     * @return object
     */
    public static function format_toc_data( $post_content, $htag_support, $toc_hierarchy )
	{
        $matches = array();
        
        if( $htag_support != '' ) {
            preg_match_all( '/(<h(['.$htag_support.']{1})[^>]*>).*<\/h\2>/msuU', $post_content, $matches, PREG_SET_ORDER );
        }
    
        if( ! empty( $matches ) ) {

            /*
            |--------------------------------------------------------------------------
            | Backtracking Algorithm Using Iteration | Main Login For Hierarchy TOC
            |--------------------------------------------------------------------------
            |
            | Initially an object with key null and empty item of arrays are inserted into the stack of arrays.
            | When inside the loop condition for the first time, the last stack value is assigned in a variable $last_data.
            | And a new node object $new_data which is instantiated and the tag number is inserted for comparison as key, and empty items
            | as a array are inserted into items property of the new node object. On the 'if' condition it checks, if the current tag number 
            | is smaller or equal to the last stack number. If the condition is true, the it enters into the while loop condition, which also 
            | checks if the current last stack tag number is greater or equal to the current tag number. It pops values from the stack until and unless the
            | condition becomes false. The while loop condition becomes false only when the last stack value tag number becomes null and the last stack number is not
            | greater or equal to the current node tag number.
            | 
            | Backtracking occurs when the current tag_number $number[2] is less than or equal to the stacks last tag_number which is assigned as $last_data
            |
            | And then the last value is inserted as the new last_data, and the new_data node is inserted into the last_data node items. 
            | Additionally the $new_data is inserted into the stack to keep track of the used node. Somehow if the if condition becomes false
            | then the stack last data is taken, and the new_data is inserted into the last_data->items and the new_data is inserted into the stack.
            | 
            */

            $stack                    = array();
            $root                     = new Node();
            $root->key                = null;
            $root->items              = array();
            $tag_counter              = 0;

            array_push( $stack, $root );

            foreach( $matches as $number ) {
                $last_data          = $stack[count($stack) - 1];
                $current_tag_number = isset( $number[2] ) ? $number[2] : '';
                $current_title      = isset( $number[0] ) ? $number[0] : '';
                $current_tag        = isset( $number[1] ) ? $number[1] : '';

                // Get The Heading Name & Heading ID using REGEX
                $heading_name = preg_replace( '/<[^<]+?>/', '', $current_title );
                $heading_name = ! empty( $heading_name ) ? strtolower( str_replace( " ", '-', preg_replace('/<[^>]+>|[^a-zA-Z\s\d]+/', "", html_entity_decode( $heading_name ) ) ) ) : '';
                preg_match('/id="(.+?)"/', $current_title, $id_matches);
				$heading_id   = isset( $id_matches[1] ) ? strtolower( $id_matches[1] ) : '';
    
                $tag_number   = ! empty( $heading_id ) ? $heading_id : ( ! empty( $heading_name ) && BetterDocs_DB::get_settings('toc_dynamic_title') != 'off' ? $heading_name : $tag_counter . '-toc-title' );

                $new_data             = new Node();
                $new_data->key        = $current_tag_number;
                $new_data->tag        = $current_tag;
                $new_data->title      = $current_title;
                $new_data->tag_number = $tag_number;
                $new_data->items      = array();

                if( $last_data->key != null && $current_tag_number <= $last_data->key ) {
                    while( $stack[count($stack) - 1]->key != null && $stack[count($stack) - 1]->key >= $current_tag_number ) {
                        array_pop( $stack );
                    }
                    $last_data = $stack[count($stack) - 1];
                }

                array_push( $last_data->items, $new_data );

                if( $toc_hierarchy != 'off' && $toc_hierarchy != '' ) {
                    array_push( $stack, $new_data );
                }

                $tag_counter++;
            }

            return $root;

        }
	}
}

new Betterdocs_TOC();