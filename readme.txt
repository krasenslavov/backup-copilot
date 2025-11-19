=== Backup Copilot - Simple WordPress Backup & Restore ===

Contributors: krasenslavov, developry
Donate Link: https://krasenslavov.com/hire-krasen/
Tags: backup, restore, migrate, database backup, export, import, site migration
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.1.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Create, restore, and migrate your WordPress site with one click. Simple backup solution with powerful features for database, files, and complete site transfers.

== DESCRIPTION ==

**Need a simple yet powerful backup solution for WordPress?**

[**Backup Copilot**](https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=readme_txt) helps you protect your WordPress site in these common scenarios:

- **Before updates** - Backup before updating WordPress, themes, or plugins
- **Site migration** - Move your site from localhost to production or between hosts
- **Development workflow** - Create restore points during active development
- **Client handoffs** - Transfer WordPress sites between developers
- **Staging sync** - Clone production to staging for safe testing
- **Pre-modification safety** - Backup before major site changes

The plugin creates complete backup points including database, themes, plugins, and media files - then lets you restore everything with one click.

https://www.youtube.com/embed/t6An3BgI6_k

### How it works:

1. Navigate to **Backup Copilot** in the WordPress admin menu
2. Click **Create** to save backup on server, or **Export** to download immediately
3. Add optional notes to identify your backups
4. Use advanced options to customize what gets backed up
5. **Restore** any backup point with a single click
6. **Download** full backup archives for transfers

**No complex configuration** - works immediately with sensible defaults that protect your entire site.

== USAGE ==

Once the plugin is installed and activated:

1. Navigate to **Backup Copilot** in the main menu to open the **Manage Backups** page.
2. Use the **Create** or **Export** buttons to generate your first backup.

Here are the steps to use [**Backup Copilot**](https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=readme_txt):

1. **Create Backup** - Saves backup on your server for quick restore
2. **Export Backup** - Downloads backup immediately to your computer
3. **Import Backup** - Upload a backup file from another site
4. **Restore Backup** - Roll back to any backup point with one click
5. **Download Full Backup** - Generate complete archive for site transfer
6. **Delete Backup** - Remove old backups to save server space

**Note:** Backups are stored in a hidden `.bkps` directory. The plugin **does not automatically upload to cloud storage** - this is a PRO feature.

== FEATURES & LIMITATIONS ==

The [**Backup Copilot**](https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=readme_txt) plugin allows you to:

- Create complete WordPress backups including database and files
- Restore your entire site to any previous backup point
- Export backups for migration to new hosts or domains
- Import backups from other WordPress installations
- Download individual components (database .sql, files .zip)
- Track backup progress in real-time
- Add notes to identify different backup points
- View backup size and creation date
- Customize backup contents with advanced options

### Advanced Options

Customize your backups with granular control:

- **Database** - MySQL database dump with all tables
- **WP-Content** - All themes, plugins, and uploads
- **Media Library** - wp-content/uploads folder
- **Themes** - All active and inactive themes
- **Plugins** - All active and inactive plugins
- **Must-Use Plugins** - wp-content/mu-plugins
- **Cache** - wp-content/cache directory
- **3rd-party Backups** - Other backup plugin files
- **.htaccess** - Server configuration (export only)
- **wp-config.php** - WordPress configuration (export only)
- **Find & Replace URLs** - Update database URLs for migrations (export only)

### Known Limitations

Free version limitations:

- Maximum backup size: **500MB** (optimal for shared hosting)
- Storage location: Server only (no automatic cloud sync)
- Scheduling: Manual backups only (no automated scheduling)
- Retention: Manual deletion only (no automatic cleanup)
- Email notifications: Not included
- Emergency rollback: Not included

== DETAILED DOCUMENTATION ==

Find step-by-step setup guides, usage instructions, demos, videos, and insights on the [**Backup Copilot**](https://backupcopilotplugin.com/help) page.

== BACKUP COPILOT PRO ==

If you're using the free version from WordPress.org and want Pro features, you can purchase the premium version on the [**Backup Copilot Pro**](https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=readme_txt) website.

Here are some features included in the Pro version:

- **Cloud Storage Integration** - Automatic sync to Dropbox, Google Drive, OneDrive
- **Automated Scheduling** - Hourly, daily, weekly, monthly backup schedules
- **Smart Retention Policies** - Age-based, count-based, and size-based auto-delete rules
- **Email Notifications** - Success and failure alerts with detailed reports
- **Unlimited Backup Size** - Handle sites of any size with resumable uploads
- **Pre-Restore Validation** - Verify backup integrity before restoration
- **Emergency Rollback** - One-click disaster recovery
- **Find & Replace URLs** - Built-in migration tool for domain changes
- **Full Multisite Support** - Network-wide and per-site backups
- **REST API** - Automate backups via API with webhook support
- **Priority Support** - Direct email and live chat support

== FREQUENTLY ASKED QUESTIONS ==

Visit the [**Support**](https://wordpress.org/support/plugin/backup-copilot/) page to share your questions or requests.

We usually respond to tickets within a few days.

Feature requests are added to our wish list and considered for future updates.

### My site crashed after an update. Can this help me recover?

Yes, if you created a backup before the update. Simply restore the most recent backup point to roll back your site to its previous state. This is why we recommend creating backups before any major changes.

### I'm moving to a new host. How do I use this plugin?

On your old site: Export a backup. On your new site: Install WordPress and Backup Copilot, then import and restore the backup. The plugin will rebuild your entire site on the new host.

### How is this different from other backup plugins?

Backup Copilot focuses on simplicity and reliability. No complex cloud configurations, no confusing scheduling options in the free version - just simple create, restore, and migrate functionality that works every time.

### Why is there a 500MB backup size limit?

This limit ensures backups complete successfully on shared hosting environments with limited resources. Most shared hosts have execution time limits that would cause larger backups to fail. The PRO version removes this limit with resumable uploads.

### Are backups deleted when I deactivate the plugin?

No. All backups remain on your server in the `.bkps` directory. Only the "Delete" action permanently removes backup files.

### Can I automate backups without the PRO version?

The free version requires manual backup creation. For automated scheduled backups, email notifications, and cloud storage, check out [**Backup Copilot Pro**](https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=readme_txt).

### Is this compatible with WordPress Multisite?

Yes! Full multisite support included. Super Admins can backup entire networks, Site Admins can backup individual sites with site-specific database tables.

### Where are backups stored?

In a hidden `.bkps` directory in your WordPress root. You may need to enable "Show hidden files" in your FTP client to see it. Backups are protected with .htaccess and index.php files.

### Do I need technical knowledge to use this plugin?

No! The plugin is designed for WordPress users of all skill levels. Click "Create" to backup, click "Restore" to recover - it's that simple. Advanced options are available for power users but not required.

### Do you offer additional support or customization?

Yes, feel free to send your request via the [**Backup Copilot**](https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=readme_txt) website.

== SCREENSHOTS ==

Below are screenshots showing how to access and use the plugin in WordPress.

1. screenshot-1.(png)
2. screenshot-2.(png)
3. screenshot-3.(png)
4. screenshot-4.(png)
5. screenshot-5.(png)
6. screenshot-6.(png)

== INSTALLATION ==

Installing the plugin is straightforward. Contact support if you encounter any issues.

= Installation from WordPress =

1. Go to **Plugins > Add New**.
2. Search for **Backup Copilot**.
3. Install and activate the plugin.
4. Click **Manage Backups** or go to **Backup Copilot** in the menu.

= Manual Installation =

1. Upload the `backup-copilot` folder to `/wp-content/plugins/`.
2. Go to **Plugins**.
3. Activate the **Backup Copilot** plugin.
4. Click **Manage Backups** or navigate to **Backup Copilot** in the menu.

= After Activation =

1. Navigate to **Backup Copilot** in the admin menu.
2. Verify all system checks show **[OK]** status.
3. Create your first backup using the **Create** button.
4. Add optional notes to identify this backup point.
5. Review the backup in the "All Backups" section.

== CHANGELOG ==

= 1.1.1 =

**Multisite Enhancements & Critical Fixes**

- **New** - Network Admin menu with multisite dashicon for WordPress Multisite installations
- **New** - Site column in backup table showing which site each backup belongs to (main site only)
- **New** - Read-only mode for main site viewing subsite backups (view-only access)
- **New** - Backward compatibility for existing backups with automatic migration
- **New** - Centralized multisite metadata storage in main site options table

- **Improved** - Multisite backup filtering now properly isolates backups per site
- **Improved** - Backup sorting now shows newest backups first in the table
- **Improved** - Delete All button now respects site isolation (only deletes own site's backups)
- **Improved** - Upload process now auto-extracts SQL files from ZIP archives
- **Improved** - Restore process handles content-only backups gracefully (skips missing SQL)
- **Improved** - Site column header alignment matches data cells in backup table

- **Fixed** - Main site can now view all subsites' backups (previously only showed own)
- **Fixed** - Subsite backups now properly filtered and isolated from other sites
- **Fixed** - Delete All no longer deletes other sites' backups in multisite
- **Fixed** - Uploaded {uuid}.zip files now restore correctly with SQL extraction
- **Fixed** - Restore failure when uploading backups without database files
- **Fixed** - Action buttons (restore/download/delete) now disabled for other sites' backups
- **Fixed** - Blog ID storage using prefixed option names to avoid UUID collisions

- **Security** - Read-only access prevents main site from modifying subsite backups
- **Security** - Proper capability checks (manage_network) for network admin access
- **Security** - Path validation and nonce verification on all multisite operations

- **Developer** - New method: `BKPC_Multisite::get_mu_option()` for reading blog IDs
- **Developer** - Enhanced `add_mu_option()` uses `switch_to_blog()` for centralized storage
- **Developer** - Updated `delete_mu_option()` handles both old and new option formats
- **Developer** - Smart SQL extraction in upload process checks ZIP contents
- **Developer** - Conditional database restore based on file existence

= 1.1.0 =

**Major Release - Complete Rebuild with Enhanced Features**

- **New** - Real-time progress tracking with toast notifications during backup operations
- **New** - Secure download handler with nonce verification for all backup files
- **New** - Restore validation system checks backup integrity before restoration
- **New** - Generate UUID handler for unique backup identification
- **New** - Progress polling system shows step-by-step backup creation status
- **New** - Pro features comparison table on settings page
- **New** - Enhanced settings page with support links and credits
- **New** - Loading bar visual feedback for all AJAX operations
- **New** - Toast notification system for success/error messages
- **New** - Modal system for confirmations and previews
- **New** - Compact mode toggle to show plugin under Tools menu
- **New** - Dashboard widget (commented out, ready for activation)

- **Improved** - Complete codebase refactoring following WordPress Coding Standards (WPCS)
- **Improved** - Modular JavaScript architecture with ES6 modules and webpack
- **Improved** - SCSS-based styling with variables and component organization
- **Improved** - All AJAX handlers now use wp_send_json_success/error for consistency
- **Improved** - Enhanced security with proper nonce verification throughout
- **Improved** - Better error handling with user-friendly messages
- **Improved** - Progress tracking now shows each step: directory creation, database save, file archiving
- **Improved** - File download URLs use secure AJAX handler instead of direct file access
- **Improved** - Settings reset functionality with confirmation dialog
- **Improved** - Backup table displays download links for all backup types
- **Improved** - Build system with Gulp + Webpack for optimized assets

- **Fixed** - Text domain consistency changed from 'wp-media-recovery' to 'backup-copilot'
- **Fixed** - Progress notice now displays structured data instead of raw text
- **Fixed** - Download backup URLs no longer show Windows file paths
- **Fixed** - Create backup AJAX response uses proper WordPress JSON format
- **Fixed** - Settings reset button now properly registered and functional
- **Fixed** - File action links (SQL, ZIP, htaccess, wp-config) work on all platforms
- **Fixed** - Backup validation checks for download-{uuid}.zip files
- **Fixed** - JavaScript TypeError when parsing AJAX responses
- **Fixed** - Loading bar classes corrected throughout JavaScript
- **Fixed** - Nonce parameter consistency (_wpnonce) across all AJAX calls

- **Security** - Added BKPC_Security class with directory protection methods
- **Security** - All file downloads now go through authenticated AJAX handler
- **Security** - File path validation ensures downloads only from backup directory
- **Security** - Nonce verification on all AJAX endpoints
- **Security** - Capability checks (manage_options) before operations

- **Developer** - New class: BKPC_Progress for file-based progress tracking
- **Developer** - New class: BKPC_Security for backup directory protection
- **Developer** - New class: BKPC_Restore_Validator for pre-restore checks
- **Developer** - Modular JavaScript files: core, timer, progressbar, notifications, modal
- **Developer** - Modular SCSS files: base/_variables, admin/*, manage-backups/*
- **Developer** - Gulp tasks for SCSS compilation, JS bundling, image optimization
- **Developer** - Webpack configuration for ES6 to ES5 transpiling
- **Developer** - All event classes properly loaded before initialization
- **Developer** - Namespace consistency: DEVRY\BKPC throughout codebase

= 1.0.0 =

- **New** - Modern modular JavaScript architecture with webpack bundling
- **New** - SCSS-based styling system with variables and componentization
- **New** - Display wp-config.php and .htaccess file icons in backup listings
- **New** - Improved button visibility - show restore/download for any backup type
- **New** - Fresh UUID generation for each backup prevents overwrites
- **New** - Enhanced backup creation logic for selective content options
- **New** - Gulp and webpack build system for asset compilation

- **Improved** - Complete codebase refactoring following WordPress coding standards
- **Improved** - Object-oriented architecture with proper namespacing
- **Improved** - Database backup now uses UUID filenames for consistency
- **Improved** - Advanced options now work correctly for all combinations
- **Improved** - ZIP creation logic respects selection accurately
- **Improved** - File icon display logic includes all backup file types
- **Improved** - Security enhancements throughout codebase

- **Fixed** - Themes + plugins only backup now creates proper ZIP files
- **Fixed** - Media/uploads backup now includes files correctly
- **Fixed** - UUID reuse causing backup overwrites
- **Fixed** - Button visibility logic changed from AND to OR for file checks
- **Fixed** - SQL filename now uses UUID instead of database name
- **Fixed** - Multisite backup file checking logic
- **Fixed** - Orphaned file display from root .bkps directory

**Check out the complete changelog on our [**Backup Copilot**](https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=readme_txt) website.**

== UPGRADE NOTICE ==

Upgrade to [**Backup Copilot Pro**](https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=readme_txt) for cloud storage, automated scheduling, email notifications, and priority support!
