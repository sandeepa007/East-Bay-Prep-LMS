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
define('DB_NAME', 'lms_packages');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'veblogy');

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
define('AUTH_KEY',         '?@dMtTs_7x:f51)kq4D!J&sJhtAS,gmL}-LQ,!aOJ]U5GZVKe8B4,vs0fENIQss_');
define('SECURE_AUTH_KEY',  'pLqGQ,2gaLvs].dD+XknNe%HjuT=Nbt :y:^7l=N%sgqhpSvu?e];9<[]`xm)Y< ');
define('LOGGED_IN_KEY',    'KAN =^S{h 4@*8}!;kz7gfwL4B1*kiWsTgoFnrH:4}W0Z;i6 T]FE(SEy.gl!VI@');
define('NONCE_KEY',        'rFQf=^nhw84PT?)@h,usu]>&6mCAyKFcH(w+qTK,X,)_z)Jz?dJ}/[INq8[h`$BU');
define('AUTH_SALT',        'vXYsWqCdpz0&j&twoXS#STmQOPd@)yb?2-dOe$X;a~$GRML?^O%l6WkXZtKjkc6D');
define('SECURE_AUTH_SALT', '}*MB5O_pJ7Cp,<bP=yZ2m)Tb$c[O8-0 ycIz7]g^YKwrD}ES=hB3vpx9fY@%6a9-');
define('LOGGED_IN_SALT',   'ey*p![obd0&y2&mkz~k.>luirx_iOYCFZHY/#p8s51[=s$?EB>VOxUb)*/xv0r8b');
define('NONCE_SALT',       'F^>bN+[{4<vpuyKFd`-GNs_OPwrDN4y*i {.>UXg(m9=y*x1X!XbX)}e[?uPtm[9');

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
define('FS_METHOD', 'direct');