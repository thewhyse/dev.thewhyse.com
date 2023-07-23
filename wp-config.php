<?php

define( 'WP CACHE', true );
define('DISABLE_WP_CRON', false);

/**
 * This config file is yours to hack on. It will work out of the box on Pantheon
 * but you may find there are a lot of neat tricks to be used here.
 *
 * See our documentation for more details:
 *
 * https://pantheon.io/docs
 */
/**
 * Pantheon platform settings. Everything you need should already be set.
 */

if (file_exists(dirname(__FILE__) . '/wp-config-pantheon.php') && isset($_ENV['PANTHEON_ENVIRONMENT'])) {
	require_once(dirname(__FILE__) . '/wp-config-pantheon.php');

/**
 * Local configuration information.
 *
 * If you are working in a local/desktop development environment and want to
 * keep your config separate, we recommend using a 'wp-config-local.php' file,
 * which you should also make sure you .gitignore.
 */

} elseif (file_exists(dirname(__FILE__) . '/wp-config-local.php') && !isset($_ENV['PANTHEON_ENVIRONMENT'])){
	# IMPORTANT: ensure your local config does not include wp-settings.php
	require_once(dirname(__FILE__) . '/wp-config-local.php');
/**
 * This block will be executed if you are NOT running on Pantheon and have NO
 * wp-config-local.php. Insert alternate config here if necessary.
 *
 * If you are only running on Pantheon, you can ignore this block.
 */
} else {
	define('DB_NAME',          'database_name');
	define('DB_USER',          'database_username');
	define('DB_PASSWORD',      'database_password');
	define('DB_HOST',          'database_host');
	define('DB_CHARSET',       'utf8');
	define('DB_COLLATE',       '');
	define('AUTH_KEY',         ']EW,]2+`VZn_sL1I^YMk;RH2-+6>W>/0&uY>xYlD@aQRkUboP83muY~3S*iZJ_@2');
	define('SECURE_AUTH_KEY',  'n)$3@3SfM|=;NcQ{rwq$L%j{xfYPod=INry2?<>.wnj-W|a!WbWyJ?F^[&8#yyqN');
	define('LOGGED_IN_KEY',    '{eNC <sE^sH5^~1_KbnKQM.#>UT}|fnWM;+[52tX/MRx} W6-SK}aJ?iWjvs=2T/');
	define('NONCE_KEY',        ' 3,^_#~-|w%qBV^5q^4495c!aaWA[BPeQ7bxkoKiYJ#s>0|PP8P}y8-+jRso&Sqw');
	define('AUTH_SALT',        'k)TOpc1vSph;8&Z2QciT*rfT6O[A71%XOKATm3.YeZZZSBOpM:*!8p|fU[x(s^k|');
	define('SECURE_AUTH_SALT', 'o[[-eO]U,-+E76DC@X!Y#=9%$detrEZBo(i .-|7l%i+4~+dwki}93Iy--?/=C-O');
	define('LOGGED_IN_SALT',   '.V;dQ }s#S9^JW^|7fW:b/u`:1<nE<vl?1XHfS|$U|Go(m$Cb!MWsf-% DC4Sx*4');
	define('NONCE_SALT',       'F|BF+{;rNlb|!edhtJ2sEVff~xy6(52a<QM-^/*#VvRE-S4cZ$&a,n8h-fZJ7l!G');
}

define('WP_CACHE_KEY_SALT', 'dev-the-agency-development.pantheonsite.io');

$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * You may want to examine $_ENV['PANTHEON_ENVIRONMENT'] to set this to be
 * "true" in dev, but false in test and live.
 */

if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );
define( 'SCRIPT_DEBUG', false );

define( 'WP_MEMORY_LIMIT', '1024M' );
define( 'WP_MAX_MEMORY_LIMIT', '1024M' );

define( 'PH_SECURE_AUTH_KEY', ' v1E?+m%~?Nv&pMaDQAR9-R7d+47A6zJ,m2Vc*-st8wC01P2GES;z,_cgF^< lKN' );


if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');