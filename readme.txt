=== Backup Copilot ===

Contributors: krasenslavov, developry
Donate Link: https://krasenslavov.com/hire-krasen/
Tags: backup, restore, migrate, database, site transfer
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.1.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Create complete site snapshots with one-click restore and migration tools. Protect database, themes, plugins, and media files.

== Description ==

Create complete site snapshots including database, themes, plugins, and media files - then restore everything with one click. No cloud accounts or complex configuration required.

https://www.youtube.com/embed/KtPA2o5FJr0

= Key Features =

* One-click snapshot creation
* One-click restore functionality
* Export and import for site migration
* Full database dumps
* File system backup
* No cloud storage required
* Manual and scheduled options

= How It Works =

1. Install and activate
2. Click Create to save a snapshot
3. Add optional notes to identify save points
4. Click Restore to roll back your site
5. Use Export to download for migration

Works immediately with sensible defaults that protect your entire installation.

= Use Cases =

* Create safety nets before updates
* Roll back after failed changes
* Migrate sites between hosts
* Transfer from localhost to production
* Recover from hacking incidents
* Protect WooCommerce store data

== Installation ==

= From Dashboard =

1. Go to Plugins > Add New
2. Search for "Backup Copilot"
3. Click Install Now, then Activate

= Manual Installation =

1. Download the plugin ZIP file
2. Upload to /wp-content/plugins/
3. Activate from Plugins menu

= After Activation =

1. Navigate to Backup Copilot in admin menu
2. Verify system checks show [OK] status
3. Click Create to make your first snapshot
4. Add optional notes to identify this point
5. Test restore process with a test snapshot

== Frequently Asked Questions ==

= How do I create a site snapshot? =

Click Backup Copilot in your admin menu, then click the Create button. The plugin saves your database and all files. No configuration needed.

= Can I restore my site after a crash? =

Yes, if you created a snapshot before the crash. Go to Manage Backups, find the restore point, and click Restore.

= What files are included? =

Snapshots include your database (MySQL), all themes, all plugins, media library (uploads folder), must-use plugins, and optionally .htaccess and wp-config.php for migration.

= How do I migrate to a new host? =

On your old site: Click Export to download. On new installation: Install this plugin, click Import, upload the file, and click Restore.

= Does this include the database? =

Yes, complete MySQL database dumps include all tables, posts, pages, custom post types, user accounts, plugin settings, and theme options.

= Where are snapshots stored? =

Snapshots are stored in a hidden `.bkps` directory in your root folder. Enable "Show hidden files" in your FTP client to see it. Protected with .htaccess and index.php security files.

= Can I schedule automatic snapshots? =

Automatic scheduling requires the Pro version. Set hourly, daily, weekly, or monthly schedules with automatic cloud storage sync.

= Does this work with WooCommerce? =

Yes, all WooCommerce data including products, orders, customers, and settings are included in database snapshots.

= Will this slow down my site? =

No, snapshot creation happens in the background. Your site remains fully accessible during the process.

= Does this support Multisite? =

Yes, Super Admins can save entire networks, Site Admins can save individual sites with site-specific database tables and files.

= What's the maximum snapshot size? =

The free version supports snapshots up to 500MB (optimal for shared hosting). The Pro version removes this limit with resumable uploads.

= Can I use this on localhost? =

Yes, works on localhost installations. Create snapshots locally, then export and import them to your production server for easy migration.

== Screenshots ==

1. screenshot-1.(png)
2. screenshot-2.(png)
3. screenshot-3.(png)
4. screenshot-4.(png)
5. screenshot-5.(png)
6. screenshot-6.(png)

== Changelog ==

= 1.1.2 =

* Fix - Reverted incorrect multisite path logic
* Fix - Removed PRO-only detection code
* Fix - Restore now works correctly for all types

= 1.1.1 =

* New - Network Admin menu for Multisite
* New - Site column in table showing origin
* New - Read-only mode for main site viewing subsites
* New - Backward compatibility with automatic migration
* Update - Improved Multisite filtering
* Update - Sorting shows newest first
* Fix - Main site can view all subsites
* Fix - Subsite isolation properly filtered
* Fix - Delete All respects site boundaries
* Fix - Uploaded files restore correctly
* Fix - Restore failure when missing database files

= 1.1.0 =

* New - Real-time progress tracking
* New - Secure download handler with nonce verification
* New - Validation system checks integrity
* New - UUID generation for unique identification
* New - Progress polling shows step-by-step creation
* New - Pro features comparison table
* New - Enhanced settings page
* New - Loading bar visual feedback
* New - Toast notification system
* New - Modal system for confirmations
* New - Compact mode toggle for admin menu
* Update - Complete codebase refactoring
* Update - Modular JavaScript interface
* Update - SCSS-based styling
* Update - Enhanced security with nonce verification
* Update - Better error handling
* Security - Added security class with directory protection
* Security - Downloads through authenticated AJAX handler
* Security - File path validation for downloads
* Security - Nonce verification on all AJAX endpoints
* Security - Capability checks before operations

= 1.0.0 =

* Initial release
* One-click creation
* One-click restore
* Site migration with export/import
* Database dumps
* File system backup
* Advanced options
* Management interface
* Real-time progress tracking

== Upgrade Notice ==

= 1.1.2 =

Critical hotfix for restore process. Recommended immediate upgrade for all users.

= 1.1.1 =

Major Multisite enhancements with improved isolation, subsite support, and critical restore fixes. Highly recommended upgrade.

= 1.1.0 =

Major release with complete rebuild, real-time progress tracking, enhanced security, and improved user interface. Recommended upgrade for all users.

= 1.0.0 =

Initial release with simple, reliable snapshot and restore functionality.
