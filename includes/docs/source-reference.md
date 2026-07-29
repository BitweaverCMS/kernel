# Kernel source reference

> Generated from the current checkout and then intended for human review.
> Paths are relative to the package root.

## Inventory summary

| Artifact | Count |
|---|---:|
| PHP files | 60 |
| Smarty templates | 61 |
| JavaScript files | 1 |
| CSS files | 0 |

## Bootstrap and schema artifacts

- `admin/schema_inc.php`
- `admin/upgrade_inc.php`
- `admin/upgrades/2.0.0.php`
- `test/bit_setup_inc.php`

## First-party classes and interfaces

- `includes/backups_lib.php:14` — `class BackupLib extends BitBase {`
- `includes/classes/BitBase.php:29` — `interface BitCacheable  {`
- `includes/classes/BitBase.php:41` — `abstract class BitBase {`
- `includes/classes/BitCache.php:11` — `class BitCache {`
- `includes/classes/BitCli.php:15` — `class BitCliArgs {`
- `includes/classes/BitDate.php:28` — `class BitDate {`
- `includes/classes/BitDbAdodb.php:39` — `class BitDbAdodb extends BitDb {`
- `includes/classes/BitDbBase.php:40` — `class BitDb {`
- `includes/classes/BitDbPear.php:33` — `class BitDbPear extends BitDb`
- `includes/classes/BitLogger.php:15` — `class BitLogger {`
- `includes/classes/BitMailer.php:39` — ` class BitMailer extends LibertyBase {`
- `includes/classes/BitSingleton.php:28` — `abstract class BitSingleton extends BitBase implements BitCacheable {`
- `includes/classes/BitSystem.php:49` — `class BitSystem extends BitSingleton {`
- `includes/classes/BitTimer.php:12` — `class BitTimer {`
- `includes/classes/HttpStatusCodes.php:11` — `class HttpStatusCodes {`
- `includes/notification_lib.php:27` — `class NotificationLib extends BitBase`
- `test/TestBitCache.php:4` — `class TestBitCache extends UnitTestCase {`
- `test/TestBitPreferences.php:4` — `class TestBitPreferences extends UnitTestCase {`
- `test/TestBitPreferencesCache.php:6` — `class TestBitPreferencesCache extends TestBitPreferences {`
- `test/TestBitPreferencesCacheDatabase.php:5` — `class TestBitPreferencesCacheDatabase extends UnitTestCase {`
- `test/TestBitPreferencesDatabase.php:6` — `class TestBitPreferencesDatabase extends UnitTestCase {`
- `test/TestBitSmartyFilter.php:10` — `class TestBitSmartyFilter extends UnitTestCase {`
- `test/TestBitSmartyFilter.php:5` — `class InputOutputTester {`

## Web-facing PHP controllers

- `admin/admin_features_inc.php`
- `admin/admin_notifications.php`
- `admin/admin_packages_inc.php`
- `admin/admin_server_inc.php`
- `admin/admin_system.php`
- `admin/apc.php`
- `admin/backup.php`
- `admin/db_performance.php`
- `admin/index.php`
- `admin/list_cache.php`
- `admin/phpinfo.php`
- `admin/remote_backup.php`
- `admin/schema_inc.php`
- `admin/sitemaps.php`
- `admin/upgrade_inc.php`
- `admin/upgrades/2.0.0.php`
- `error.php`
- `error_simple.php`
- `icons/index.php`
- `index.php`
- `modules/mod_adsense.php`
- `modules/mod_package_menu.php`
- `modules/mod_powered_by.php`
- `modules/mod_side_menu.php`
- `modules/mod_time.php`
- `requirements_graph.php`
- `test/TestBitCache.php`
- `test/TestBitDatabase.php`
- `test/TestBitPreferences.php`
- `test/TestBitPreferencesCache.php`
- `test/TestBitPreferencesCacheDatabase.php`
- `test/TestBitPreferencesDatabase.php`
- `test/TestBitSmartyFilter.php`
- `test/bit_setup_inc.php`
- `view_cache.php`

## Declared schema tables

- `adodb_logsql`
- `kernel_config`
- `mail_notifications`
- `sessions`

## Plugin and module directories

- `modules/`

## Templates

- `modules/help_mod_adsense.tpl`
- `modules/help_mod_package_menu.tpl`
- `modules/help_mod_powered_by.tpl`
- `modules/help_mod_time.tpl`
- `modules/mod_admin_menu.tpl`
- `modules/mod_adsense.tpl`
- `modules/mod_application_menu.tpl`
- `modules/mod_bitweaver_info.tpl`
- `modules/mod_bottom_bar.tpl`
- `modules/mod_package_icons.tpl`
- `modules/mod_package_menu.tpl`
- `modules/mod_packages.tpl`
- `modules/mod_powered_by.tpl`
- `modules/mod_server_stats.tpl`
- `modules/mod_side_menu.tpl`
- `modules/mod_site_title.tpl`
- `modules/mod_time.tpl`
- `modules/mod_top_menu.tpl`
- `modules/mod_twitter_feed.tpl`
- `templates/admin.tpl`
- `templates/admin_features.tpl`
- `templates/admin_include_anchors.tpl`
- `templates/admin_menu_options.tpl`
- `templates/admin_notifications.tpl`
- `templates/admin_packages.tpl`
- `templates/admin_server.tpl`
- `templates/admin_sitemaps.tpl`
- `templates/admin_system.tpl`
- `templates/ajax_file_browser_inc.tpl`
- `templates/backup.tpl`
- `templates/bit_left.tpl`
- `templates/bit_right.tpl`
- `templates/bot_bar.tpl`
- `templates/box.tpl`
- `templates/confirm.tpl`
- `templates/dynamic.tpl`
- `templates/error.tpl`
- `templates/error_ticket.tpl`
- `templates/feedback_inc.tpl`
- `templates/footer.tpl`
- `templates/footer_inc.tpl`
- `templates/force_installer.tpl`
- `templates/html.tpl`
- `templates/html_head_inc.tpl`
- `templates/json_output.tpl`
- `templates/list_cache.tpl`
- `templates/menu_kernel_admin.tpl`
- `templates/menu_side_admin_inc.tpl`
- `templates/menu_top_admin_inc.tpl`
- `templates/minifind.tpl`
- `templates/navbar.tpl`
- `templates/pagination.tpl`
- `templates/poptop.tpl`
- `templates/popup_box.tpl`
- `templates/server_stats_inc.tpl`
- `templates/side_bar.tpl`
- `templates/sitemap.tpl`
- `templates/top.tpl`
- `templates/top_bar.tpl`
- `templates/upload_slot_inc.tpl`
- `templates/view_cache.tpl`

## Reading cautions

- Presence in this inventory does not make a file a supported public API.
- Bundled third-party libraries must be distinguished from package-owned code.
- Base schema files do not prove the migration state of a deployed database.
- Controllers may rely on include files, globals, services, and template callbacks not visible from their filename alone.
