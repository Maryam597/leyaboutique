<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u721650116_bbxPN' );

/** Database username */
define( 'DB_USER', 'u721650116_wOOXX' );

/** Database password */
define( 'DB_PASSWORD', 'rnO8cyZnI2' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '()O|KKx_$}#AGrM@9XoI_g[3MtrYsLJ>}2jV<zy7a:c/q5p_0V}FsO$N5!`g`0Pr' );
define( 'SECURE_AUTH_KEY',   'LVLas-EYWXyFWty.&f$K4GBQtrnkrsjD#lCOQ4,:F@T:F|EdGqX@p(O&>-cW2|&-' );
define( 'LOGGED_IN_KEY',     '7&0K3T{<1~CwECD`/(r5`#zw7$Hm >+I9DaODk&IlJr_Eh6WpM;#CC^Wu}D;`]ye' );
define( 'NONCE_KEY',         'I/G.,{iSA/dt:%wJB14g^+H=M75*{{d+qUC%dW3.z2HHt/:EvMLp2{>4^>~K0Nva' );
define( 'AUTH_SALT',         '9MHyfaae8gx*mFBJcw`gtK@Zcg|W:a)R)3Zj7c.IgcHEmU-m`Oq?K`[z4Cw&757q' );
define( 'SECURE_AUTH_SALT',  '`3>k1OT$AcA93 F!+*M00D{`~yedh ?vX]=; BjV9?MB~[v RaB)|_--)tt/TgzI' );
define( 'LOGGED_IN_SALT',    'yA3Vo/px|C#_>9lqjRo?PLt8Z|4]@ZN-$~bO(.e!5d%H kO&YFlt1g>^p*wm]:*Z' );
define( 'NONCE_SALT',        'I>W~!QUOj6l6FW 4 >E8vZkW5ssC;O7]X?=c.D1gS}asO+5_R w`G;^[Gg?S)1^K' );
define( 'WP_CACHE_KEY_SALT', '04KuxFl$XHO5B;TwzOCU{>T5_67@|~Fcw&&H!?!E8f!bb<RoIXBod;r/blBl%0oK' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
