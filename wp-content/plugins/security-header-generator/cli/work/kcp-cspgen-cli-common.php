<?php
/** 
 * Common CLI Methods
 * 
 * Set of common methods for the CLI
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// we also only want to allow CLI access
defined( 'WP_CLI' ) || die( 'Only CLI access allowed' );

// make sure the class doesn't already exist
if( ! class_exists( 'KCP_CSPGEN_CLI_Common' ) ) {

    /** 
     * Class KCP_CSPGEN_CLI_Common
     * 
     * The actual class for generating the common methods
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Security Header Generator
     * 
    */
    class KCP_CSPGEN_CLI_Common {

        /** 
         * cli_success
         * 
         * The method is responsible for displaying a success message in CLI
         * 
         * @since 7.4
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @param string $_msg The message to display
         * 
         * @return void This method does not return anything
         * 
        */
        public static function cli_success( string $_msg ) : void {

            // throw the error
            WP_CLI::success( __( $_msg ) );

        }

        /** 
         * cli_error
         * 
         * The method is responsible for displaying a error message in CLI
         * 
         * @since 7.4
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @param string $_msg The message to display
         * 
         * @return void This method does not return anything
         * 
        */
        public static function cli_error( string $_msg ) : void {

            // throw the error
            WP_CLI::error( __( $_msg ) );

        }

        /** 
         * cli_warning
         * 
         * The method is responsible for displaying a warning message in CLI
         * 
         * @since 7.4
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @param string $_msg The message to display
         * 
         * @return void This method does not return anything
         * 
        */
        public static function cli_warning( string $_msg ) : void {

            // throw the error
            WP_CLI::warning( __( $_msg ) );

        }

        /** 
         * get_the_cli_csp_post
         * 
         * Get our CPT for the CSP
         * 
         * @since 7.4
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return object This method returns a nullable object
         * 
        */
        public static function get_the_cli_csp_post( ) : ?object {

            // hold our cache key
            $_cache_key = 'wpsh_cli_csp_post';

            // check if it's already cached
            if( wp_cache_get( $_cache_key, $_cache_key ) ) {

                // return it
                return wp_cache_get( $_cache_key, $_cache_key );

            // it's not
            } else {

                // get our CPT data, if it exists
                $_rs = new WP_Query( array( 'post_type' => 'kcp_csp', 'posts_per_page' => 1, 'post_status' => 'draft' ) );

                // if it exists cache it for an hour then return it
                if( $_rs ) {

                    // cache it for an hour
                    wp_cache_set( $_cache_key, $_rs, $_cache_key, HOUR_IN_SECONDS );

                    // now return it
                    return $_rs;

                }

                // default to null
                return null;
                
            }

        }

    }

}
