### Backup Copilot

Contributors: krasenslavov
Donate Link: https://krasenslavov.com/hire-krasen/#donate-sponsor
Tags: backup copilot, transfer, migrate, restore, import, export
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.2
Stable tag: 0.6.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Quickly and easily create backup points of your WordPress installation to restore, export, or transfer to another location.

## DESCRIPTION

Quickly and easily create backup points of your WordPress installation to restore, export, or transfer to another location.

**_Backup Copilot_ is still its alpha version. Please don't add negative or 1-star reviews. We would appreciate it if you Contact Us directly via our website, [**Krasen Slavov**](https://krasenslavov.com/), or better use the WP.org plugin [**Support Tab**](https://wordpress.org/support/plugin/backup-copilot/) page.**

_Backup Copilot_ will guide you through your migration and WordPress transfer process.

It is a tool that will make your life easier when you need to migrate your website to another server.

It is a single-page plugin, and we tried to make it as user-friendly and straightforward to use as possible.

No need to set up or choose from numerous settings and options. Please install and activate the plugin, go to the main page, and start using it. It will let you know about each step during the backup process and inform you when the process is done and how much it took.

https://www.youtube.com/embed/t6An3BgI6_k

## UASGE

First and foremost, to use _Backup Copilot_, you need to have all four checks with **`[OK]`** next to them.

_If any of them show **`[Failed]`**, you need to contact your hosting provider to either change your server permissions or, in rare cases like WPEngine server settings where they need to increase ServerTimeout limit temporarily._

You can always contact us and use the plugin [**Support Tab**](https://wordpress.org/support/plugin/backup-copilot/) for additional help.

**Who is Backup Copilot for?**

The plugin is specifically created for users that need to transfer and migrate their website to another location. However, if you are a developer, you can use it to start back up points during your development process and restore them at any time.

**How do I create/export WordPress backup?**

You will see that the main actions on the plugin page are _Create_ and _Export_. The only significant difference between the two is that when you click on _Create_, your backup is stored on the server, and when you click on _export_, you will be prompted to download the backup.

After that, the backup point will be removed and no longer available on the server.

In addition, once you click on _, Advanced Options_, you will see that you can _Find and Replace URLs_ from your database. This is only available for _Export_ action and is helpful if you want your database populated with a different URL for your migration.

Another field from __Advanced Option__ is where you save `.htaccess` and `wp-config.php` files within your backup. This is used **ONLY** for the _Create_ action. And this might be something you need during your development if you make any changes to these two files.

Moreover, you will see that there are numerous other _Advanced Options_ that will allow you to customize the contents of your backup points additionally.

Lastly, if you want to have your backups identified, you can easily add some _Notes_that are stored as a text file within your backup directory and won't be lost while you transfer your backups.

**How do I transfer my WordPress website?**

Once you have your backup exported and saved on your computer, you may go ahead and install _Backup Copilot_ on the target destination.

After that, you can select and upload the file, which will show directly under the **All Backups** section after the import is completed.

**How do I manage my WordPress backups?**

In the last section of the main plugin page, you can see all of your backups with several action buttons used for management.

* **Restore** - restore your backup point
* **Generate Full Download** - generate a full archive for your backup directory and show a new action for download
* **Delete** - delete all backup files
* **Download Full Backup** - save a zip archive for your backup (same contents as export action) if you decide to transfer it to another location
* **Download Database** - save database file only
* **Download WP Content** - save a zip archive with `wp-content` directory _(`uploads/site/[id]` for multisite)_
* **Open Notes** - see the notes you have added when creating the backup point

_Hold your mouse over each icon to view the full description for the action._

**Who has access to Backup Copilot?**

If you are the primary and only Admin user on the site, you shouldn't have full access to _Backup Copilot_.

However, if you have multiple Admin users created and want to be the only one who has access to _Backup Copilot_. Then, use the plugin access option at the bottom when you _Add New or Edit_ Admin users.

This is also useful to restrict access for Admins on multisite.

## FEATURES

Some numerous actions and options are included in the free version of the plugin.

**Main Actions**

* Create and Export backup point.
* Upload and Import backup point.
* Restore backup point at any given time.
* Delete and remove backup point.
* Generate full download with all the files in your backup directory.
* Download the backup point to transfer to another location.
* Save SQL database file.
* Save contents archive (`wp-contents` or `uploads/sites/[id]` for multisite).
* Add notes to differentiate your backup points easily.

**Advanced Options**

* Save `.htaccess` and `wp-config.php` files with your backup.
* Find and replace _URLs_ before export.
* Exclude media library `wp-content/uploads` from your backup.
* Exclude must use plugins `wp-content/mu-plugins` from your backup.
* Exclude must use plugins `wp-content/plugins` from your backup.
* Include cache `wp-content/cache` to your backup.
* Include 3rd-party backups to your backup.
* Exclude/include SQL `database` from your backup.
* Exclude/include `wp-contents` from your backup.

_Advanced Options are not available for individual sites within WordPress multisite._

**User Settings**

Enable or disable Admin user access to _Backup Copilot_.

The setting is located at the bottom of each user page, either when you Add New or Edit users.

This will allow you to have a single _Admin-Only_ access for a standard WordPress setup.

OR enable/disable access to the plugin for WordPress multisite Admin users.

_See screenshot-4 and screenshot-6_

## DETAILED DOCUMENTATION

Additional information with step-by-step setup, usage, demos, and support can be found on the [**Krasen Slavov**](https://krasenslavov.com/) website.

## BACKUP COPILOT PRO

As of yet, this plugin doesn't have a commercial version available.

We are working on a version with a whole lot of features.

For example, three premium features we want to include:

* Export a standard WordPress site and then import it in WordPress multisite without causing conflicts.
* Store your backups on the cloud or in your __Dropbox.com__ account.
* Manage the PHP configuration variables within your WP Admin area.

So if you want to stay in touch, visit [**Krasen Slavov**](https://krasenslavov.com/) to subscribe to our newsletter, get notified, and learn more about the premium version.

## FREQUENTLY ASKED QUESTIONS

Use the [**Support Tab**](https://wordpress.org/support/plugin/backup-copilot/) on this page to post your requests and questions.

All tickets are usually addressed within 24 hours.

If your request is an add-on feature, we will add it to the plugin wish-list and consider implementing it in the next major version.

### Do you remove backups if I delete/deactivate the plugin?

**No**, all backups are kept on the server. Only a `delete` backup action will remove the files.

For example, if you have created some backups and decide to deactivate and delete the plugin, and then at some point you install it again, you will have all access to all previously created backups.

### Why do you change the PHP configurations?

We need to ensure the backups created, exported, and imported aren't corrupted.

These configurations are removed from all files once the plugin is deactivated.

### Are there any limitations?

Yes, **you cannot create backups over 500MB**, and when it comes to server restrictions, you need to have all four checks **`[OK]`** at the bottom of the page.

In addition, some managed WordPress hosting providers like _WPEngine_ have _Server Timeout_ limits (e.g., 60sec) for `admin-ajax.php,` which may cause corrupted backup files.

We have tested, and depending on the server load, it works most of the time for sites up to 500MB. However, a few times, we got corrupted backups for sites less than 500MB because the server killed our backup process.

### Do you store anything in the database?

**No**, for regular WordPress sites.

**Yes**, for multisite, but only a boolean that goes with each unique backup ID. This is deleted/removed a not affect any backup actions.

### Does it support WordPress multisite?

**Yes**, there is full WordPress multisite support.

* Super Admin can create full backups from the main site, precisely as it works on a standard WordPress setup.
* Admins can create backups for their sites. However, the backup _WILL_ only have the site-specific database tables and `uploads/sites/id` directory.
* All __Advanced Options__ are for Admins, and to allow access to Backup Copilot, Admin needs to have **User can have access to Backup Copilot** enabled.

_See screenshot-5 and screensho-6_

### Where are my backups located?

They are hidden in the `.bkps` directory, inside the root WordPress directory.

This is how most servers are configured by default, and you need to turn dot file visibility manually the same way you do for `.htaccess.`

### Who has access to the plugin pages?

Only _Administrators_ and _Super Administrators_ have access to the Backup Copilot pages.

However, we have added a feature that will allow you to limit the access to the plugin for a single _Admin-Only_ for websites with multiple Admin accounts.

To do that, when you add a new Admin, you will see that you can select if they can access _Backup Copilot_.

This field can also be turned on/off when you edit Admin user profiles.

### Do you offer additional support if I encounter any issues?

Yes, you can contact us by using the Contact form @ [**Krasen Slavov**](https://krasenslavov.com/) website.

## SCREENSHOTS

1. screenshot-1.(png)
2. screenshot-2.(png)
3. screenshot-3.(png)
4. screenshot-4.(png)
5. screenshot-5.(png)
6. screenshot-6.(png)

## INSTALLATION

The plugin installation process is standard and easy to follow. Please let us know if you have any difficulties with the installation.

= Standard Installation =

1. Visit **Plugins > Add New**.
2. Search for **Backup Copilot**.
3. Install and activate the **Backup Copilot** plugin.

= Manual Installation =

1. Upload the entire `backup-copilot` directory to the `/wp-content/plugins/` directory.
2. Visit **Plugins**.
3. Activate the **Backup Copilot** plugin.

= After Activation =

1. Click on the **Manage Backups** link on the main Plugin page or go to **Backup Copilot** from the main Admin menu.

## CHNAGELOG

= 0.6 =

- Update - test and check functionality with WordPress 6.1

= 0.5 =

- New - add full WordPress multisite support
- New - super Admin can create full Multisite support for the Main blog
- Update - all other blogs backups are created only for site Uploads and site-specific database tables

= 0.4 =

- New - visualize system and configuration information
- New - add 500MB max upload file size and max post file size to (within .htaccess, php.ini, and .user.ini)
- New - add step by step notification for backup export the same as create
- Update - re-work export -> download process
- Update - Automate plugin `assets/build` js and CSS files creation
- Update - Move all Ajax methods into a separate file _backup-copilot-middleware.js_
- Fix - time elapsed as a human-readable string

= 0.3 =

- New - add an upper limit of 500MB for backups
- Update - find and replace to match URLs only
- Update - single Admin access for WordPress multisite
- Update - Limit .htaccess and wp-config.php save only for local backups
- Fix - multisite support download links for actions

= 0.2 =

- New - added WordPress Mu support and checks when you display backups
- Fix - restrict access to Administrator users on sites with multi-sites Admin users

= 0.1 =

- Initial release and first commit into the WordPress.org SVN

## UPGRADE NOTICE

_None_