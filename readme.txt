=== Backup Copilot - Database Backup & Restore ===

Contributors: krasenslavov
Donate Link: https://krasenslavov.com/hire-krasen/#donate-sponsor
Tags: backup, database backup, restore, migrate, export, import, database migration
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional WordPress database backup and restore plugin. Create, export, migrate, and restore your WordPress site with one click. Full multisite support.

== DESCRIPTION ==

**Backup Copilot** is a powerful yet simple WordPress database backup and restore plugin that helps you create secure backup points, migrate your website, and restore your WordPress installation with ease.

=== Key Features ===

- **One-Click Database Backup** - Create complete WordPress database backups instantly
- **Site Migration Made Easy** - Export and import your entire WordPress site to any location
- **Instant Restore** - Roll back to any backup point with a single click
- **Advanced Backup Options** - Customize what gets backed up (database, themes, plugins, media)
- **Find & Replace URLs** - Automatically update URLs when migrating to new domain
- **WordPress Multisite Support** - Full support for WordPress multisite installations
- **Secure Storage** - Backups stored in hidden `.bkps` directory
- **Progress Tracking** - Real-time backup creation progress
- **Access Control** - Limit plugin access to specific admin users
- **Zero Configuration** - Works out of the box with sensible defaults

=== Perfect For ===

- **Website Migrations** - Move your WordPress site to a new host or domain
- **Development Workflow** - Create restore points during development
- **Site Transfers** - Clone your site to staging or production environments
- **Pre-Update Safety** - Backup before WordPress core, theme, or plugin updates
- **Client Handoffs** - Transfer WordPress sites between developers
- **Regular Backups** - Create scheduled backup points for peace of mind

=== How It Works ===

**Creating Backups**

1. Click "Create" to store backup on server
2. Click "Export" to download backup immediately
3. Add optional notes to identify your backups
4. Use advanced options to customize backup contents

**Migrating Your Site**

1. Export backup from source site
2. Install Backup Copilot on destination site
3. Upload backup file
4. Restore with one click

**Managing Backups**

- View all backups with creation date and size
- Download individual components (database, wp-content)
- Generate full backup archives for transfer
- Restore any backup point instantly
- Delete old backups to save space

https://www.youtube.com/embed/

== ADVANCED OPTIONS ==

Customize your backups with powerful advanced options:

- **Save .htaccess** - Include server configuration in backups
- **Save wp-config.php** - Include WordPress configuration
- **Find & Replace URLs** - Update database URLs for migrations (export only)
- **Media Library** - Include/exclude wp-content/uploads
- **Themes** - Include active and inactive themes
- **Plugins** - Include active and inactive plugins
- **Must-Use Plugins** - Include wp-content/mu-plugins
- **Cache** - Include/exclude wp-content/cache
- **3rd-party Backups** - Include other backup plugin files
- **Database** - Include/exclude database tables
- **WP-Content** - Include/exclude entire wp-content directory

== WORDPRESS MULTISITE SUPPORT ==

Full WordPress multisite compatibility:

- **Super Admin** - Create full network backups from main site
- **Site Admin** - Create site-specific backups with own database tables
- **Granular Access** - Control which admin users can access Backup Copilot
- **Network Isolation** - Each site's backups remain separate

== USER ACCESS CONTROL ==

Restrict plugin access for enhanced security:

- Enable/disable access per admin user
- Perfect for multi-admin WordPress sites
- Control access in WordPress multisite networks
- Manage permissions from user profile pages

== TECHNICAL DETAILS ==

- **Maximum Backup Size**: 500MB (optimal for most shared hosting)
- **Storage Location**: Hidden `.bkps` directory in WordPress root
- **Database Engine**: Uses mysqldump via PHP for reliable exports
- **Compression**: ZIP compression for efficient storage
- **Naming**: UUID-based backup identification
- **Security**: WP nonce verification, capability checks, sanitized inputs

== REQUIREMENTS ==

Your server must meet these requirements:

- PHP ZipArchive extension
- Database connection with backup privileges
- Write permissions on WordPress root directory
- Write permissions on wp-content directory

The plugin will display status checks on the main page. If any check fails, contact your hosting provider.

== DOCUMENTATION ==

For detailed documentation, video tutorials, and support visit:
[Backup Copilot Documentation](https://krasenslavov.com/plugins/backup-copilot/)

== BACKUP COPILOT PRO ==

Coming soon with premium features:

- **Cloud Storage** - Save backups to Dropbox, Google Drive, Amazon S3
- **Scheduled Backups** - Automated daily, weekly, monthly backups
- **Email Notifications** - Get notified when backups complete
- **WP to Multisite** - Convert standard WordPress to multisite
- **PHP Configuration Manager** - Manage PHP settings from WordPress admin
- **Larger Backups** - Support for sites over 500MB
- **Priority Support** - Get help directly from the developer

[Join Newsletter](https://krasenslavov.com/) to be notified when Pro version launches.

== FREQUENTLY ASKED QUESTIONS ==

= Are backups deleted when I deactivate the plugin? =

No. All backups remain on your server in the `.bkps` directory. Only the "Delete" action removes backup files.

= Why does the plugin modify PHP configuration? =

To ensure backups aren't corrupted, we temporarily adjust PHP settings during backup operations. These are restored when the plugin is deactivated.

= What are the backup size limitations? =

Maximum backup size is 500MB. This works reliably on most shared hosting environments. Some managed hosts (like WPEngine) may have additional timeout restrictions.

= Does it store data in the WordPress database? =

No data is stored for standard WordPress installations. For multisite, we store only a boolean flag per backup UUID for network isolation.

= Can I use this with WordPress Multisite? =

Yes! Full multisite support included. Super Admins can backup entire networks, Site Admins can backup individual sites.

= Where are backups stored? =

In a hidden `.bkps` directory in your WordPress root. You may need to enable "Show hidden files" in your FTP client to see it.

= Who can access the plugin? =

Only Administrator and Super Administrator roles by default. You can further restrict access using the user permission setting.

= Do you offer support? =

Yes! Use the [Support Forum](https://wordpress.org/support/plugin/backup-copilot/) or [contact us directly](https://krasenslavov.com/).

== SCREENSHOTS ==

1. screenshot-1.(png)
2. screenshot-2.(png)
3. screenshot-3.(png)
4. screenshot-4.(png)
5. screenshot-5.(png)
6. screenshot-6.(png)

== INSTALLATION ==

= Automatic Installation =

1. Go to **Plugins > Add New**
2. Search for **Backup Copilot**
3. Click **Install Now**
4. Click **Activate**

= Manual Installation =

1. Download the plugin ZIP file
2. Go to **Plugins > Add New > Upload Plugin**
3. Choose the downloaded ZIP file
4. Click **Install Now**
5. Click **Activate**

= After Activation =

1. Go to **Backup Copilot** in the WordPress admin menu
2. Verify all system checks show **[OK]**
3. Create your first backup!

== CHANGELOG ==

= 1.0.0 =

This is a major rewrite of Backup Copilot with significant improvements to code quality, architecture, and user experience.

- **New** - Modern modular JavaScript architecture with webpack bundling
- **New** - SCSS-based styling system with variables and componentization
- **New** - WordPress dashboard widget showing backup stats and recent backups
- **New** - Display wp-config.php and .htaccess file icons in backup listings
- **New** - Improved button visibility - show restore/download for any backup type
- **New** - Fresh UUID generation for each backup prevents overwrites
- **New** - Enhanced backup creation logic for selective content options
- **New** - Onboarding notice for new users with quick start guide
- **New** - Gulp and webpack build system for asset compilation
- **New** - npm package management for development dependencies

- **Improved** - Complete codebase refactoring following WordPress coding standards
- **Improved** - Object-oriented architecture with proper namespacing
- **Improved** - Database backup now uses UUID filenames for consistency
- **Improved** - Download URL generation with proper slash handling for Windows servers
- **Improved** - Advanced options now work correctly for all combinations
- **Improved** - ZIP creation logic - no longer requires "content" checkbox for selective backups
- **Improved** - Folder filtering in ZIP archives respects selection accurately
- **Improved** - File icon display logic includes all backup file types
- **Improved** - HTML structure and layout for backup listings (single table instead of multiple)
- **Improved** - Security enhancements throughout codebase

- **Fixed** - Themes + plugins only backup now creates proper ZIP files
- **Fixed** - Media/uploads backup now includes files correctly
- **Fixed** - UUID reuse causing backup overwrites
- **Fixed** - Button visibility logic changed from AND to OR for file checks
- **Fixed** - SQL filename now uses UUID instead of database name
- **Fixed** - Backup restoration file path references
- **Fixed** - Download backup file path references
- **Fixed** - View file existence checks for UUID-based naming
- **Fixed** - Multisite backup file checking logic
- **Fixed** - Dashboard widget recent backups glob pattern with trailing slash
- **Fixed** - Orphaned file display from root .bkps directory
- **Fixed** - HTML table structure causing layout breaks
- **Fixed** - Removed error_log call from production code

- **Updated** - Plugin class renamed from backup-copilot.php to class-backup-copilot.php
- **Updated** - Multisite class renamed to class-bkpc-multisite.php for consistency
- **Updated** - Asset file paths from `assets/build/` to `assets/dist/`
- **Updated** - JavaScript file from backup-copilot.min.js to bkpc-admin.min.js
- **Updated** - CSS file from backup-copilot.min.css to bkpc-admin.min.css
- **Updated** - Localization script handle to match new naming convention
- **Updated** - Dashboard CSS enqueuing for widget display

- **Developer** - New gulpfile.js for task automation
- **Developer** - New webpack.config.js for JavaScript bundling
- **Developer** - New package.json with all development dependencies
- **Developer** - Split JavaScript into modular files (core, ajax-handler, actions, ui)
- **Developer** - Split SCSS into modular files (base, admin components)
- **Developer** - Added npm scripts: `npm run build`, `npm run watch`
- **Developer** - Removed old rebuild.bat and watch.bat files

= 0.6.2 =

- Update - Test and verify functionality with WordPress 6.4

= 0.6.0 =

- Update - Test and check functionality with WordPress 6.1

= 0.5.0 =

- New - Add full WordPress multisite support
- New - Super Admin can create full multisite backups for main blog
- Update - Blog backups created only for site uploads and site-specific database tables

= 0.4.0 =

- New - Visualize system and configuration information
- New - Add 500MB max upload file size and max post file size (within .htaccess, php.ini, and .user.ini)
- New - Add step by step notification for backup export
- Update - Re-work export to download process
- Update - Automate plugin asset build files creation
- Update - Move all Ajax methods into separate file
- Fix - Time elapsed as human-readable string

= 0.3.0 =

- New - Add upper limit of 500MB for backups
- Update - Find and replace to match URLs only
- Update - Single admin access for WordPress multisite
- Update - Limit .htaccess and wp-config.php save only for local backups
- Fix - Multisite support download links for actions

= 0.2.0 =

- New - Added WordPress multisite support and checks when displaying backups
- Fix - Restrict access to Administrator users on sites with multi-admin users

= 0.1.0 =

- Initial release and first commit into WordPress.org SVN
