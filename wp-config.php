<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'pearlsvibe' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'k[Vlm=!n5sr*^HGVZ?61k!5IIrHjuubP0h,0G.`2%s/{ n/Q#):iRQeuv5Q<Zh-!' );
define( 'SECURE_AUTH_KEY',  't@/:*`|6-&`X2/#am}fxn]Q,LvFTuF:uA>#EPzD)xJv}qR|Z!4{G#=nts;uh>HW~' );
define( 'LOGGED_IN_KEY',    'S]:bj74![Rq-4~H&W9L3;ZGlDcr0~hfF*jnfmQ)ZjUv*vT}&`u;>Ojc&|sO(}t?0' );
define( 'NONCE_KEY',        'Qy1d^%Vz_7yoldH,e9!lDK*,]]re[^>yiRazsO ].Dvv,J*NR HVX/&eBWjZ)8|L' );
define( 'AUTH_SALT',        '7KXi hxO,DX5ga| R,oO]G]TH:8UIheA!?iWAJBHHSA~HA_eL22e=@>!`g`$z0HI' );
define( 'SECURE_AUTH_SALT', '1uBT;jme#J2M;1K!^5)gv.8.?!HizE!/<:5aZYWvZj)@ivn)Weg5Xi>H?Bp_V4`m' );
define( 'LOGGED_IN_SALT',   '0QZ{DY,=!87=(,M<?CsV/k}((Cw-B,JDuv%uripa`6,oW<Y`8 AgqY6{N7(XHKLw' );
define( 'NONCE_SALT',       'fR=pcGXxUDF|5=4C*r<$3Po%v=FhsD@?bF#AN<A*xG{nEkFLe}:GDYG1(`(j~<F7' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
