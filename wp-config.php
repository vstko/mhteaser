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
define('DB_NAME', 'mhteaser');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'qYW#dz;A}BUi`? F8xD{<5lI.B]*:Be8EB@E7R9p=TY#IWSJ%~2%:I1TXqxb.NXJ');
define('SECURE_AUTH_KEY',  '~zBF|b/Zs4T&=PDbLIL@~ <$Pab#3l1Pi0}SZ5(6+T9CG~*[g2PQwhzN54ug1DsP');
define('LOGGED_IN_KEY',    '[}Qi;_VylfB0cJsY._ m2^_Mxc~I!DS!P%NJ+dptw} IymPc64cyd5gD(bE%tF.v');
define('NONCE_KEY',        'h%&F%c#tLy!Y~S6xT#G84j0/1MthA$CWg=m9o60E[B(4qTi{bBBBQz[(0f{F[@@R');
define('AUTH_SALT',        'Z<ffC__f!_#4?ca4_Y^ZYG%bZfcl,^-y*y:N%fY{:k-^B+h*Dr+v*H8YAEIL}(!h');
define('SECURE_AUTH_SALT', '<Ig/s`1{WrlCmUR4q{_azjArm.I.,z0ov}vX&fQAL582pD?)[P9|#.9un`PW-z`{');
define('LOGGED_IN_SALT',   'a!o1na9gvM={x[JVrKTaz^kQ29^eYKq-{Qn#A-rI+OIvK@FM1W|rAzKwMV|ryCsC');
define('NONCE_SALT',       'rQR`9YGO]$ZWgPr;-K~va@n|x)uq_D^,ZCh_?9LjUO?USx;w@@2pseWks?[JL)/e');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
