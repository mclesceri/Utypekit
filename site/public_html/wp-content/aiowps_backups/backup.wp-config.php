<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/storage/av03339/www/public_html/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'av03339_cbp');

/** MySQL database username */
define('DB_USER', 'av03339');

/** MySQL database password */
define('DB_PASSWORD', 'yS8T.525t4GyH');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'fuqmwdlkeu0zhl9m4wsbphhdzt9hruqpnr7gheybzd93na4nqpkh0ga3bbd8vubv');
define('SECURE_AUTH_KEY',  'r2tlmu2aob13orppqvztc2vzivek4s4a370xzwnfcdpcvkeji2hmnzoicsizsx95');
define('LOGGED_IN_KEY',    '1ur2ooovegdpscb5r6cmvcpk8owyvemw5aulatwpj7coqoy0ljietavz30mglisk');
define('NONCE_KEY',        'fcp4mzml0agwaeecf7nxgx1mn5epcxfskr5bx2fci1l6qd5wxwe3hjqqa9zfeqjq');
define('AUTH_SALT',        'simzolftdomlxc2lie5dqvwbdhdsmt3q8ilv9zvs5qtmutpu8rbwqaka9o0vtacz');
define('SECURE_AUTH_SALT', 'cih8rtwn7oxkdrafu6b5podriirlmgknnjs28abf2k658n4fjbvsc9kpi6fxlthv');
define('LOGGED_IN_SALT',   'rapzusqwcj30bvy2tn3eeu9kqgfrvg0lvubgbckgwdzh7y3qv6r5nxob6i0mbi9n');
define('NONCE_SALT',       'mdvjyjochkc6d0g1a11dvmcnr1ikafs0dxfourtkzlghnuglxqi4q2x3sxteoj4b');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);



/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
