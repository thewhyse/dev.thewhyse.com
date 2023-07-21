(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Image Generator
	blocks.registerBlockType(
		'trx-addons/igenerator',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'AI Helper Image Generator', "trx_addons" ),
			description: __( "AI Helper Image Generator form for frontend", "trx_addons" ),
			icon: 'images-alt2',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					model: {
						type: 'string',
						default: 'openai/default'
					},
					show_settings: {
						type: 'boolean',
						default: false
					},
					demo_thumb_size: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_demo_thumb_size']	//'trx_addons-thumb-avatar'
					},
					demo_images: {
						type: 'string',
						default: ''
					},
					demo_images_url: {
						type: 'string',
						default: ''
					},
					prompt: {
						type: 'string',
						default: ''
					},
					prompt_width: {
						type: 'number',
						default: 100
					},
					button_text: {
						type: 'string',
						default: ''
					},
					number: {
						type: 'number',
						default: 3
					},
					columns: {
						type: 'number',
						default: 3
					},
					size: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_default_image_size']
					},
					align: {
						type: 'string',
						default: ''
					},
					tags_label: {
						type: 'string',
						default: __( 'Popular Tags', "trx_addons" )
					},
					tags: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/igenerator' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_igenerator'] )
								},
								// Model
								{
									'name': 'model',
									'title': __( 'Default model', "trx_addons" ),
									'descr': __( "Select a default model for generation images", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_models'] )
								},
								// Show settings
								{
									'name': 'show_settings',
									'title': __( 'Show settings', "trx_addons" ),
									'descr': __( "Show a button to show a model selector", "trx_addons" ),
									'type': 'boolean'
								},
								// Number
								{
									'name': 'number',
									'title': __( 'Generate at once', "trx_addons" ),
									'descr': __( "Specify the number of images to be generated at once (from 1 to 10)", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 10
								},
								// Columns
								{
									'name': 'columns',
									'title': __( 'Columns', "trx_addons" ),
									'descr': __( "Specify the number of columns to show images (from 1 to 12)", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 12
								},
								// Size
								{
									'name': 'size',
									'title': __( 'Image size', "trx_addons" ),
									'descr': __( "Size of generated images", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_image_sizes'] )
								},
								// Default prompt
								{
									'name': 'prompt',
									'title': __( 'Default prompt', "trx_addons" ),
									'type': 'text'
								},
								// Prompt width
								{
									'name': 'prompt_width',
									'title': __( 'Prompt field width', "trx_addons" ),
									'descr': __( "Specify a width of the prompt field (in %)", "trx_addons" ),
									'type': 'number',
									'min': 50,
									'max': 100
								},
								// Button text
								{
									'name': 'button_text',
									'title': __( 'Button text', "trx_addons" ),
									'type': 'text'
								},
								// Align
								{
									'name': 'align',
									'title': __( 'Alignment', "trx_addons" ),
									'descr': __( "Alignment of the prompt field and tags", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_aligns'] )
								},
								// Tags label
								{
									'name': 'tags_label',
									'title': __( 'Tags label', "trx_addons" ),
									'type': 'text'
								},
								// Demo Image
								{
									'name': 'demo_images',
									'name_url': 'demo_images_url',
									'title': __( 'Demo images', "trx_addons" ),
									'descr': __( "Selected images will be used instead of the image generator as a demo mode when limits are reached", "trx_addons" ),
									'type': 'image',
									'multiple': true
								},
								// Demo thumb size
								{
									'name': 'demo_thumb_size',
									'title': __( 'Thumb size', "trx_addons" ),
									'descr': __( "Select a thumb size to show images", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_thumb_sizes'] )
								},
							], 'trx-addons/igenerator', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							trx_addons_gutenberg_add_param_title( props, true ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				// Get child block values of attributes
				if ( props.hasOwnProperty( 'innerBlocks' ) ) {	// && props.innerBlocks.length
					props.attributes.tags = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/igenerator'
	) );

	// Register block Tag Item
	blocks.registerBlockType(
		'trx-addons/igenerator-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Tag Item', "trx_addons" ),
			description: __( "Insert a tag for Image Generator", "trx_addons" ),
			icon: 'tag',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/igenerator'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Tag Item attributes
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				prompt: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/igenerator-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Tag', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Enter title of the tag", "trx_addons" ),
									'type': 'text'
								},
								// Prompt
								{
									'name': 'prompt',
									'title': __( 'Prompt', "trx_addons" ),
									'descr': __( "Enter a prompt associated with a tag", "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/igenerator-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/igenerator-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
