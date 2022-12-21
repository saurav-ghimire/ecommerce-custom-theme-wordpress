<?php
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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'yuldzit_mra' );

/** Database username */
define( 'DB_USER', 'yuldzit_mra' );

/** Database password */
define( 'DB_PASSWORD', 'mf4a@Tx8GHfB' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '9v|+-v-ecY !cWz1qu{z;[1_6FH<(^ui9]AHoyT];PQ%Bhe$RX7FgmAnA]!hKzvC' );
define( 'SECURE_AUTH_KEY',  'MU:}1v1>xC]xjWS&4,f{{}d^j8mcN/EWH8P/m,BJaW+E5Hd!c2;Ab6w+u,lMXDio' );
define( 'LOGGED_IN_KEY',    'D!hlu`$.jf~pk5 N/gtWB5!Zt0H5L>.%D/E.RfTvdOB|[NQK&~!bzea[ytt>^XuA' );
define( 'NONCE_KEY',        '6raw$9*k!).XV}L}d5*KaZyG&Gbk_]+.>78,)jYjcc;kl,S,#[*PM~+**ND-iF%`' );
define( 'AUTH_SALT',        ';<16pei|1V=F@l7lDgLzZ>6~na~S|Jx$Y#/`QD_#yZ_EhM<FeVJxaU4<sID[f%7+' );
define( 'SECURE_AUTH_SALT', '3sr/R#Z^k=9HcFX;j_6K`q;P#{x%?4.,3<ejID^g>=ley]V>-q9 s),+g|PjM^1+' );
define( 'LOGGED_IN_SALT',   '{8p`D<Kbd=#-_!Q:>/Z4t$u;)K([62$.jE4m]^qlN;og[A9B!5KS#0 mYv-/KK!X' );
define( 'NONCE_SALT',       'D3gTdu5/@2v4;8@#X0Rv oz5D1:>|m8rAlk*oP./.@F1Ux^n7*H$B9J%+fRGd3f=' );

/**#@-*/

/**
 * WordPress database table prefix.
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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
