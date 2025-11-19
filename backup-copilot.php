<?php
/**
 * Plugin Name:  Backup Copilot
 * Plugin URI: https://backupcopilotplugin.com
 * Description: Simple and powerful WordPress backup and restore plugin. Backup your database and files, restore with one click, and migrate your site effortlessly.
 * Version: 1.1.1
 * Author: Krasen Slavov
 * Author URI: https://developry.com/
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: backup-copilot
 * Domain Path: /languages
 *
 * Copyright (c) 2018 - 2025 Developry Ltd. (email: contact@developry.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Environment constant - controls asset loading
 * Set to 'dev' for development, 'prod' for production
 */
define( __NAMESPACE__ . '\BKPC_ENV', 'prod' );

/**
 * Minimum requirements
 */
define( __NAMESPACE__ . '\BKPC_MIN_PHP_VERSION', '7.2' );
define( __NAMESPACE__ . '\BKPC_MIN_WP_VERSION', '5.0' );

/**
 * Core constants
 */
define( __NAMESPACE__ . '\BKPC_PLUGIN_UUID', 'bkpc' );
define( __NAMESPACE__ . '\BKPC_PLUGIN_TEXTDOMAIN', 'backup-copilot-pro' );
define( __NAMESPACE__ . '\BKPC_PLUGIN_NAME', 'Backup Copilot' );
define( __NAMESPACE__ . '\BKPC_PLUGIN_VERSION', '1.1.1' );
define( __NAMESPACE__ . '\BKPC_PLUGIN_DOMAIN', 'https://backupcopilotplugin.com' );


/**
 * WPORG and site page URLs
 */
define( __NAMESPACE__ . '\BKPC_PLUGIN_DOCS_URL', 'https://backupcopilotplugin.com/help' );
define( __NAMESPACE__ . '\BKPC_PLUGIN_SUPPORT_URL', 'https://backupcopilotplugin.com/contact' );
define( __NAMESPACE__ . '\BKPC_PLUGIN_WPORG_SUPPORT_URL', 'https://wordpress.org/support/plugin/backup-copilot/#new-topic' );
define( __NAMESPACE__ . '\BKPC_PLUGIN_WPORG_RATE_URL', 'https://wordpress.org/support/plugin/backup-copilot/reviews/#new-post' );

/**
 * Plugin paths
 */
define( __NAMESPACE__ . '\BKPC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( __NAMESPACE__ . '\BKPC_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( __NAMESPACE__ . '\BKPC_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\BKPC_PLUGIN_BACKUP_DIR_PATH', trailingslashit( wp_normalize_path( ABSPATH . '.bkps' ) ) );
define( __NAMESPACE__ . '\BKPC_PLUGIN_WPCONTENT_DIR_PATH', trailingslashit( ABSPATH . 'wp-content' ) );

/**
 * Database
 * Use WordPress database constants
 */
define( __NAMESPACE__ . '\BKPC_DB_HOSTNAME', DB_HOST );
define( __NAMESPACE__ . '\BKPC_DB_NAME', DB_NAME );
define( __NAMESPACE__ . '\BKPC_DB_USER', DB_USER );
define( __NAMESPACE__ . '\BKPC_DB_PASSWORD', DB_PASSWORD );

/**
 * Free version limitations
 */
define( __NAMESPACE__ . '\BKPC_MAX_BACKUP_SIZE', 500 * 1024 * 1024 ); // 500MB limit for free version

/**
 * Allowed HTML for wp_kses sanitization
 */
define(
	__NAMESPACE__ . '\BKPC_PLUGIN_ALLOWED_HTML_ARR',
	wp_json_encode(
		array(
			'a'      => array(
				'href'   => array(),
				'title'  => array(),
				'class'  => array(),
				'target' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'span'   => array(
				'class' => array(),
			),
			'div'    => array(
				'class' => array(),
			),
			'p'      => array(
				'class' => array(),
			),
		)
	)
);

/**
 * Environment-based image URL routing
 */
if ( 'dev' === BKPC_ENV ) {
	define( __NAMESPACE__ . '\BKPC_PLUGIN_IMG_URL', BKPC_PLUGIN_DIR_URL . 'assets/dev/images/' );
} else {
	define( __NAMESPACE__ . '\BKPC_PLUGIN_IMG_URL', BKPC_PLUGIN_DIR_URL . 'assets/dist/img/' );
}

/**
 * Load library main classes
 */
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/class-bkpc-admin.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/class-mysqldump.php';

/**
 * Load manage backup core classes
 */
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/core/class-bkpc-fs.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/core/class-bkpc-db.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/core/class-bkpc-utils.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/core/class-bkpc-view.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/core/class-bkpc-zip.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/core/class-bkpc-multisite.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/core/class-bkpc-progress.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/core/class-bkpc-security.php';

/**
 * Load manage backups event classes
 */
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/events/class-bkpc-delete-backup.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/events/class-bkpc-create-backup.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/events/class-bkpc-download-backup.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/events/class-bkpc-export-backup.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/events/class-bkpc-restore-backup.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/events/class-bkpc-restore-validator.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/library/events/class-bkpc-upload-backup.php';

/**
 * Load admin module
 */
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/admin.php';
