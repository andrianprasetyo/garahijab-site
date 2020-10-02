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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'garahijab' );

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
define( 'AUTH_KEY',         '90d6.@yPFimgP}LP?*:=XWFeuF%Fsx9Sy[g_OG60|id%l&cB#-~N6)prv V)6VO6' );
define( 'SECURE_AUTH_KEY',  'x;6QPr3=pyQ8C)Kp]h,S-,5=U&jrjD2/*VoP;fEG_+j~Dr}KtGh7ZMDXqyZJFgkd' );
define( 'LOGGED_IN_KEY',    'd~!hjHBL%X/lin]{`erxd8VZ6~kFtcez-r2_`)*~Er(Cd;sgsSzIyKOR7?cX*y:0' );
define( 'NONCE_KEY',        'HA}Xy-{YO}EKci>s1UTS:P5z3x`+j$[Q,c<EGd<VC7`_rh`!o)9kE97XK(2qEZ19' );
define( 'AUTH_SALT',        'sm1;C7g~av^^NWWCd,JU~u#M|oz0,~r_ua[b{G]AiGXPmQp+Y3G19QPmV6NEi n|' );
define( 'SECURE_AUTH_SALT', 'kAX*a:fU(6[j9D>|tG{#y=pu)WLgvAM[4xQ}sf[/Q?f1!VyWg`rsij*YDGeS3:vj' );
define( 'LOGGED_IN_SALT',   'ezt&yjyX|]v0.{~T|w2U*ZQ^a`9aQ6{RcVUVe%H]}S D`J.`*o^n9*ScD6j5lX5C' );
define( 'NONCE_SALT',       'MHMz>_ki/(@TU-08I_AD.SAR?=]*2<j-tks0V#n`~u(>apS[/R~LEJ*3DV8s!J[e' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
