<?php
/** 
 * Header Processor
 * 
 * Controls and processes the necessary configured headers
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// make sure the class doesn't already exist
if( ! class_exists( 'KCP_CSPGEN_Headers' ) ) {

    /** 
     * Class KCP_CSPGEN_Headers
     * 
     * The actual class for generating and processing our headers
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Security Header Generator
     * 
    */
    class KCP_CSPGEN_Headers {

        /** 
         * kp_process_headers
         * 
         * The method is responsible for processing the headers
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return void This method does not return anything
         * 
        */
        public function kp_process_headers( ) : void {

            // implement hook
            do_action( 'wpsh_pre_headers' );

            // add in the front-end headers
            $this -> add_the_frontend_headers( );

            // add the admin headers if we're configured to do so
            $this -> add_the_admin_headers( );

            // implement hook
            do_action( 'wpsh_post_headers' );

        }

        /** 
         * add_the_frontend_headers
         * 
         * The method is responsible for adding the headers to the front end
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return void This method does not return anything
         * 
        */
        private function add_the_frontend_headers( ) : void {

            // hook into our headers action
            add_action( 'send_headers', function( ) : void { 

                // get our generated headers
                $_gen_headers = $this -> kp_populate_header_array( );

                // loop over the generated header and add it
                foreach( $_gen_headers as $_k => $_v ) {

                    // try to remove the existing header if there is one
                    header_remove( $_k );

                    // add the header, and try to replace the existing header if it exists
                    header( $_k . ': ' . $_v, true );

                }

                // implement hook
                do_action( 'wpsh_send_frontend_headers' );

            }, PHP_INT_MIN );

        }

        /** 
         * add_the_admin_headers
         * 
         * The method is responsible for adding the headers to the admin side
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return void This method does not return anything
         * 
        */
        private function add_the_admin_headers( ) : void {

            // we are, so let's hook into an admin specific action where we can apply them
            add_action( 'admin_init', function( ) : void {

                // get our generated headers
                $_gen_headers = $this -> kp_populate_header_array( );

                // loop over the generated header and add it
                foreach( $_gen_headers as $_k => $_v ) {

                    // try to remove the existing header if there is one
                    header_remove( $_k );

                    // add the header, and try to replace the existing header if it exists
                    header( $_k . ': ' . $_v, true );
                }

                // implement hook
                do_action( 'wpsh_send_admin_headers' );

            }, PHP_INT_MAX );

        }

        /** 
         * kp_process_headers_for_display
         * 
         * The method is responsible for processing the headers for public display only
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array Returns an array of all headers configured
         * 
        */
        public function kp_process_headers_for_display( ) : array {
            
            // just return
            return $this -> kp_populate_header_array( );
        }

        /** 
         * kp_populate_header_array
         * 
         * The method is responsible for processing and configuring the headers
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array Returns an array of all headers configured
         * 
        */
        private function kp_populate_header_array( ) : array {

            // setup the return array
            $_ret = array( );

            // set the apply to admin flag for the standard security headers
            $_admin_apply = filter_var( get_our_option( 'apply_to_admin' ), FILTER_VALIDATE_BOOLEAN ) && is_admin( );

            // set the apply to admin flag for the content security headers
            $_admin_csp_apply = filter_var( get_our_option( 'apply_csp_to_admin' ), FILTER_VALIDATE_BOOLEAN ) && is_admin( );

            // set the apply to admin flag for the feature policy headers
            $_admin_fp_apply = filter_var( get_our_option( 'apply_fp_to_admin' ), FILTER_VALIDATE_BOOLEAN ) && is_admin( );

            // include STS setting
            $_apply_sts = filter_var( get_our_option( 'include_sts' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include the Strict Transport Security header
            if( $_apply_sts ) {

                // get our directives, and set defaults if they are not set
                $_age = ( get_our_option( 'include_sts_max_age' ) ) ? get_our_option( 'include_sts_max_age' ) : 31536000;
                $_include = ( get_our_option( 'include_sts_subdomains' ) ) ? 'includeSubdomains;' : '';
                $_preload = ( get_our_option( 'include_sts_preload' ) ) ? 'preload;' : '';

                // trim the last semi-colon if needed
                if( $_include && $_preload ) {

                    $_extras = rtrim( $_include . ' ' . $_preload, ';' );
                } else {

                    // if either include or preload, remove the last semi-colon
                    if( $_include ) {

                        $_extras = rtrim( $_include, ';' );
                    } else {

                        $_extras = rtrim( $_preload, ';' );
                    }
                }

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    // set the header with directives
                    $_ret['Strict-Transport-Security'] = "max-age=$_age; $_extras";

                    // implement hook with the header argument
                    do_action( 'wpsh_sts_header', $_ret['Strict-Transport-Security'] );

                }

            }

            // include expect CT setting
            $_apply_ect = filter_var( get_our_option( 'include_expectct' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configure to include the expect-ct header
            if( $_apply_ect ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    // set the header
                    $_ret['Expect-CT'] = 'max-age=604800, enforce';

                    // implement hook with the header argument
                    do_action( 'wpsh_expectct_header', $_ret['Expect-CT'] );

                }

            }

            // include Options setting
            $_apply_ofs = filter_var( get_our_option( 'include_ofs' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include the from options header
            if( $_apply_ofs ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    // set the header with our configured directives
                    $_ret['X-Frame-Options'] = ( get_our_option( 'include_ofs_type' ) ) ? get_our_option( 'include_ofs_type' ) : 'DENY';
                
                    // implement hook with the header argument
                    do_action( 'wpsh_ofs_header', $_ret['X-Frame-Options'] );

                }

            }

            // include ACO setting
            $_apply_aco = filter_var( get_our_option( 'include_acao' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include the access control origin
            if( $_apply_aco ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    // setup the origin
                    $_origin = ( get_our_option( 'include_acao_origin' ) ) ?: '*';

                    $_ret['Access-Control-Allow-Origin'] = $_origin;

                    // implement hook with the header argument
                    do_action( 'wpsh_acao_header', $_ret['Access-Control-Allow-Origin'] );

                }
            
            }

            // include mimesniffing setting
            $_apply_mimesniffing = filter_var( get_our_option( 'include_mimesniffing' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include nosniff
            if( $_apply_mimesniffing ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    $_ret['X-Content-Type-Options'] = 'nosniff';

                    // implement hook with the header argument
                    do_action( 'wpsh_mimesniffing_header', $_ret['X-Content-Type-Options'] );

                }

            }

            // include referrer setting
            $_apply_rp = filter_var( get_our_option( 'include_referrer_policy' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include referrer policy
            if( $_apply_rp ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    $_ret['Referrer-Policy'] = ( get_our_option( 'include_referrer_policy_setting' ) ) ? get_our_option( 'include_referrer_policy_setting' ) : 'strict-origin';
                
                    // implement hook with the header argument
                    do_action( 'wpsh_referrer_header', $_ret['Referrer-Policy'] );

                }

            }

            // include download setting
            $_apply_dl = filter_var( get_our_option( 'include_download_options' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include forced downloads
            if( $_apply_dl ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    $_ret['X-Download-Options'] = 'noopen';

                    // implement hook with the header argument
                    do_action( 'wpsh_dlopt_header', $_ret['X-Download-Options'] );

                }

            }

            // include cross domain origins setting
            $_apply_cdo = filter_var( get_our_option( 'include_crossdomain' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include cross domain origins
            if( $_apply_cdo ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    $_ret['X-Permitted-Cross-Domain-Policies'] = 'none';

                    // implement hook with the header argument
                    do_action( 'wpsh_crossdomain_header', $_ret['X-Permitted-Cross-Domain-Policies'] );

                }

            }

            // include cross Cross-Origin-Embedder-Policy setting
            $_apply_coep = filter_var( get_our_option( 'coep' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include the Cross-Origin-Embedder-Policy
            if( $_apply_coep ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    // append the header
                    $_ret['Cross-Origin-Embedder-Policy'] = ( get_our_option( 'coep_setting' ) ) ? get_our_option( 'coep_setting' ) : 'unsafe-none';
                
                    // implement hook with the header argument
                    do_action( 'wpsh_coep_header', $_ret['Cross-Origin-Embedder-Policy'] );

                }
                
            }

            // include cross Cross-Origin-Opener-Policy setting
            $_apply_coop = filter_var( get_our_option( 'coop' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to include the Cross-Origin-Opener-Policy
            if( $_apply_coop  ) {

                // make sure this header should be added
                if( $_admin_apply || ( ! is_admin( ) ) ) {

                    // append the header
                    $_ret['Cross-Origin-Opener-Policy'] = ( get_our_option( 'coop_setting' ) ) ? get_our_option( 'coop_setting' ) : 'unsafe-none';
                
                    // implement hook with the header argument
                    do_action( 'wpsh_coop_header', $_ret['Cross-Origin-Opener-Policy'] );

                }
                
            }

            // see if we're configured to include the Cross-Origin-Resource-Policy
            if( get_our_option( 'corp' ) ) {

                // append the header
                $_ret['Cross-Origin-Resource-Policy'] = ( get_our_option( 'corp_setting' ) ) ? get_our_option( 'corp_setting' ) : 'same-origin';
            
                // implement hook with the header argument
                do_action( 'wpsh_corp_header', $_ret['Cross-Origin-Resource-Policy'] );
                
            }

            // include cross Feature Policy setting
            $_apply_fp = filter_var( get_our_option( 'feature_policy' ), FILTER_VALIDATE_BOOLEAN );

            // see if we're configured to apply a Feature Policy/Permissions Policy
            if( $_apply_fp ) {

                // make sure this header should be added
                if( $_admin_fp_apply || ( ! is_admin( ) ) ) {

                    // append it to the permissions policy header if it already exists
                    if( isset( $_ret['Permissions-Policy'] ) ) {

                        // append
                        $_ret['Permissions-Policy'] .= $this -> kp_permissions_policy_builder( );

                    // otherwise just populate it
                    } else {

                        // populate
                        $_ret['Permissions-Policy'] = $this -> kp_permissions_policy_builder( );

                    }

                }

            }

            // if there is a permissions policy, make sure to lose the last ', '
            if( isset( $_ret['Permissions-Policy'] ) ) {

                // drop the last ', '
                $_ret['Permissions-Policy'] = rtrim( $_ret['Permissions-Policy'], ', ' );

                // implement hook with the header argument
                do_action( 'wpsh_permissions_header', $_ret['Permissions-Policy'] );
            }

            // include cross contect securtiy Policy setting
            $_apply_csp = filter_var( get_our_option( 'generate_csp' ), FILTER_VALIDATE_BOOLEAN );

            // check if we're actually generating a content security policy
            if( $_apply_csp ) {

                // make sure this header should be added
                if( $_admin_csp_apply || ( ! is_admin( ) ) ) {

                    // fire up our chunk holder
                    $_chunk = '';

                    // generate our full CSP, if we're configured to do so
                    $_chunk .= $this -> kp_csp_builder( );

                    // include cross contect securtiy Policy setting
                    $_apply_upgrade = filter_var( get_our_option( 'include_upgrade_insecure' ), FILTER_VALIDATE_BOOLEAN );

                    // see if we're configured to upgrade all requests, if so.. append it to the chunk.  IIS doesn't allow duplicate keys
                    if( $_apply_upgrade ) {

                        // the iis header
                        $_chunk .= ' upgrade-insecure-requests;';
                    }

                    // add the content security policy header
                    $_ret['Content-Security-Policy'] = $_chunk;
                    $_ret['X-Content-Security-Policy'] = $_chunk;

                    // implement hook with the header argument
                    do_action( 'wpsh_csp_header', $_ret['Content-Security-Policy'] );

                }

            } else {

                // include cross contect securtiy Policy setting
                $_apply_upgrade = filter_var( get_our_option( 'include_upgrade_insecure' ), FILTER_VALIDATE_BOOLEAN );

                // see if we're configured to upgrade all requests, if so.. append it to the chunk.  IIS doesn't allow duplicate keys
                if( $_apply_upgrade ) {

                    // make sure this header should be added
                    if( $_admin_csp_apply || ( ! is_admin( ) ) ) {

                        // add the content security policy header
                        $_ret['Content-Security-Policy'] = 'upgrade-insecure-requests;';
                        $_ret['X-Content-Security-Policy'] = 'upgrade-insecure-requests;';

                        // implement hook with the header argument
                        do_action( 'wpsh_upgradesecure_header', 'upgrade-insecure-requests;' );

                    }

                }
                
            } 
            
            // return the generated array
            return $_ret;

        }

        /** 
         * kp_csp_builder
         * 
         * The method is responsible for building the content security policy header string
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return string Returns the generated string
         * 
        */
        private function kp_csp_builder( ) : string {

            // implement the hook
            do_action( 'wpsh_pre_csp_generate' );

            // setup our return string
            $_ret = '';

            // get the allowed directives
            $_directives = KCP_CSPGEN_Common::get_csp_directives( );

            // get the values from the CLI pulls
            $_cli = $this -> kp_get_generated_csp( );
            
            // I know we have the directives so just loop them
            foreach( $_directives as $_key => $_val ) {

                // get the directive value URI's
                $_uris = get_our_option( $_val['id'] );

                // get the unsafe config
                $_unsafe = get_our_option( $_val['id'] . '_allow_unsafe' );

                // get the cli array element, default to a blank array
                $_cli_element = ( $_cli[$_val['id']] ) ?? array( );

                // hold the defaults
                $_defaults = $this -> kp_csp_wp_defaults( $_val['id'] );

                // hold an unsafe string
                $_us = '';

                // hold a uri string
                $_uri_str = '';

                // append it to the output string only if there is something to append
                if( ! empty( $_uris ) || ! empty( $_defaults ) || ! empty( $_cli_element ) ) {

                    // check if the unsafe is not empty
                    if( is_array( $_unsafe ) ) {

                        // check for the unsafe inline
                        if( in_array( 1, $_unsafe ) ) {

                            // append
                            $_us .= " 'unsafe-inline' ";

                        }

                        // check for the unsafe eval
                        if( in_array( 2, $_unsafe ) ) {

                            // append
                            $_us .= " 'unsafe-eval' ";

                        }

                    }

                    // check if the cli element is not empty
                    if( ! empty( $_cli_element ) ) {

                        // loop them
                        foreach( $_cli_element as $_uri ) {

                            // make a populate the new string out of them
                            $_uri_str .= ' ' . $_uri . ' ';

                        }

                    // otherwise
                    } else {

                        // empty the string
                        $_uri_str = '';

                    }

                    // append it with the self
                    $_ret .= $_key . " 'self' " . $_us . $this -> remove_duplicates( $_uris . $_uri_str ) . $_defaults . "; ";

                } else {

                    // check if the unsafe is not empty
                    if( is_array( $_unsafe ) ) {

                        // check for the unsafe inline
                        if( in_array( 1, $_unsafe ) ) {

                            // append
                            $_us .= " 'unsafe-inline' ";

                        }

                        // check for the unsafe eval
                        if( in_array( 2, $_unsafe ) ) {

                            // append
                            $_us .= " 'unsafe-eval' ";

                        }

                    }

                    // append the empty CSP directive, allow self
                    $_ret .= $_key . " 'self' " . $_us . "; ";

                }

            }

            // implement the post generation hook
            do_action( 'wpsh_post_csp_generate', $_ret );

            // return the string
            return $_ret;

        }

        /** 
         * kp_csp_wp_defaults
         * 
         * The method is responsible for returning the wordpress default URI's per setting where applicable
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @param string $_setting The setting to check
         * 
         * @return string Returns the generated string
         * 
        */
        private function kp_csp_wp_defaults( string $_setting ) : string {

            // hold the return string
            $_ret = '';

            // get our defaults allowed option
            $_defaults_allowed = filter_var( get_our_option( 'include_wordpress_defaults' ), FILTER_VALIDATE_BOOLEAN );

            // check if we should include the default wordpress stuff
            if( $_defaults_allowed ) {

                // utilize a switch for this
                switch( $_setting ) {

                    case 'generate_csp_custom_styles': // styles
                    case 'generate_csp_custom_styles_elem': // styles
                            $_ret = ' *.googleapis.com *.gstatic.com ';
                        break;
                    case 'generate_csp_custom_scripts': // scripts
                    case 'generate_csp_custom_scripts_elem': // scripts
                        $_ret = ' *.g.doubleclick.net *.google-analytics.com *.google.com *.googletagmanager.com *.gstatic.com ';
                        break;
                    case 'generate_csp_custom_fonts':
                        $_ret = ' *.gstatic.com *.bootstrapcdn.com ';
                        break;
                    case 'generate_csp_custom_images':
                        $_ret = ' *.googletagmanager.com *.w.org *.gravatar.com *.google.com *.google-analytics.com *.gstatic.com ';
                        break;
                    case 'generate_csp_custom_connect':
                        $_ret = ' *.google-analytics.com *.wpengine.com yoast.com *.google.com *.g.doubleclick.net ';
                        break;
                    case 'generate_csp_custom_frames':
                        $_ret = ' *.g.doubleclick.net *.google.com *.fls.doubleclick.net ';
                        break;
                }

            }

            // return it
            return $_ret;

        }

        /** 
         * kp_permissions_policy_builder
         * 
         * The method is responsible for building the feature/permission policy header string
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return string Returns the generated string
         * 
        */
        private function kp_permissions_policy_builder( ) : string {

            // hold a return string
            $_ret = '';

            // get the permissions directives
            $_directives = KCP_CSPGEN_Common::get_permissions_directives( );

            // get the configured options
            $_options = get_our_option( 'feature_policies' );

            // I know we have them, so just loop over them
            foreach( $_directives as $_key => $_val ) {

                // get the configured options directive, default to any
                $_dir = ( $_options[$_val['id']] ) ?? 1;

                // get the configured options domain
                $_url = $_options['fp_' . $_key . '_src_domain'];

                // append the policy to the string
                $_ret .= $this -> kp_format_policy_directive( $_key, $_dir, $_url ) . ', ';

            }

            // return the compiled string, minus the last comma
            return rtrim( $_ret, ', ' );

        }

        /** 
         * kp_format_policy_directive
         * 
         * The method is responsible for generating the actual policy based on it's settings configured
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @param string $_policy The policy to generate
         * @param int $_directive The directive to implement
         * @param string $_url The url to include if necessary
         * 
         * @return string Returns the generated string
         * 
        */
        private function kp_format_policy_directive( string $_policy, int $_directive, ?string $_url = '' ) : string {

            // use a switch
            switch( $_directive ) {
                case 0: // none
                    return "$_policy=()";
                    break;
                case 1: // any
                    return "$_policy=*";
                    break;
                case 2: // self
                    return "$_policy=(self)";
                    break;
                case 3: // source
                    return "$_policy=($_url)";
                    break;
                default: // any
                    return "$_policy=*";
                    break;
            }

        }

        /** 
         * kp_get_generated_csp
         * 
         * The method is responsible for retrieving the CLI generated resources
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return string Returns the generated array
         * 
        */
        private function kp_get_generated_csp( ) : array {

            // hold our cache key
            $_cache_key = 'wpsh_cli_csp';

            // hold the return array
            $_ret = array( );

            // check if it's already cached
            if( wp_cache_get( $_cache_key, $_cache_key ) ) {

                // set it
                $_ret = wp_cache_get( $_cache_key, $_cache_key );

            // it's not
            } else {

                // get our CPT data, if it exists
                $_qry = new WP_Query( array( 'post_type' => 'kcp_csp', 'posts_per_page' => 1, 'post_status' => 'draft' ) );
                $_rs = $_qry -> get_posts( );

                // if it exists: css, script, connect, frame, media, font, img, default
                if( $_rs ) {

                    // populate the generated CSP
                    $_ret = maybe_unserialize( $_rs[0] -> post_content );
                }

                // cache it for an hour
                wp_cache_set( 'wpsh_cli_csp', $_ret, 'wpsh_cli_csp', HOUR_IN_SECONDS );
                
            }

            // return it
            return ( is_array( $_ret ) ) ? $_ret : ( array ) $_ret;
        }

        /** 
         * remove_duplicates
         * 
         * The method is responsible for removing duplicates from the generated strings
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @param string $_str The original string
         * 
         * @return string Returns the generated string
         * 
        */
        protected function remove_duplicates( string $_str ) : string {

            // remove the duplicate strings
            $_str = implode( ' ', array_unique( explode( ' ', $_str ) ) );

            // return 
            return $_str;

        }

    }

}