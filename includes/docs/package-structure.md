# Bitweaver package structure (session dump)

Starter notes for a later pass. Not a complete inventory. Runtime bootstrap
belongs in [core-runtime.md](core-runtime.md). How to *write* package docs
belongs in [package-documentation-plan.md](package-documentation-plan.md).
This file is the **on-disk and UI conventions** an agent needs to add a
feature package without rediscovering Kernel’s discovery rules.

Flesh out from Kernel + a few public packages (`users`, `themes`, `liberty`).
Do not copy install-only OEM behavior here.

---

## On-disk layout (typical optional package)

```
<pkg>/
  includes/bit_setup_inc.php     # required — Kernel scan file
  includes/classes/
  includes/docs/                 # engineering docs; deny HTTP (see security.md)
  templates/                     # Smarty; bitpackage:<pkg>/file.tpl
  admin/                         # schema, installer upgrades, admin controllers
  css/
  index.php                      # and other web controllers at package root
  .htaccess                      # pretty URLs for this package
```

Directory basename and registered `package_name` can differ. Constants and
URLs use `basename(package_path)`. Identity and permissions use
`package_name` (usually lowercase).

---

## Registration (`includes/bit_setup_inc.php`)

`BitSystem::scanPackages()` loads each active package’s
`includes/bit_setup_inc.php`. That file must call `registerPackage()`:

```php
$gBitSystem->registerPackage( array(
	'package_name' => 'example',
	'package_path' => dirname( dirname( __FILE__ ) ).'/',
	// optional: 'homeable' => TRUE, 'service' => …, 'required_package' => TRUE
) );
```

That defines (among others):

| Constant | Typical value |
|---|---|
| `EXAMPLE_PKG_NAME` | registered name |
| `EXAMPLE_PKG_DIR` | checkout basename |
| `EXAMPLE_PKG_PATH` | filesystem path, trailing slash |
| `EXAMPLE_PKG_URL` | web path, trailing slash |
| `EXAMPLE_PKG_INCLUDE_PATH` | `…/includes/` |
| `EXAMPLE_PKG_CLASS_PATH` | `…/includes/classes/` |
| `EXAMPLE_PKG_ADMIN_PATH` | `…/admin/` |

Active vs installed vs registered vs loaded: see core-runtime.md. Config key
`package_<name>` is `y` / `i` / `n`. Controllers that need the package call
`$gBitSystem->verifyPackage( 'example' )`.

Optional: `registerAppMenu()` for the **application** (customer) nav bar.
That is **not** the Administration menu (below).

---

## Web controllers

- First line of a page: `require_once( '../kernel/includes/setup_inc.php' );`
  (path relative to the controller). Do not invent a second bootstrap.
- Then `verifyPackage` / `verifyPermission` / load domain objects.
- HTML out: `$gBitSystem->display( 'bitpackage:<pkg>/<tpl>.tpl', $title )`.
  Admin screens often pass `array( 'display_mode' => 'admin' )`.
- JSON/raw: `outputJson()` / `outputRaw()`, not `display()`.
- Business logic stays in classes under `includes/classes/`. Templates do not
  own it. User-facing strings: `tra()`.

---

## Administration menu (easy to miss)

Two Kernel surfaces list **package admin** links. Neither is
`registerAppMenu()`. Both **discover a template by naming convention**:

| Surface | Code | Template |
|---|---|---|
| Top **Administration** dropdown | `themes/includes/menu_register_inc.php` | `bitpackage:<pkg>/menu_<pkg>_admin.tpl` |
| Kernel admin home panels | `kernel/admin/index.php` (no `?page=`) | same |

Rules observed:

1. File **must** exist: `templates/menu_<package>_admin.tpl`.
2. Package must be **active** (`kernel` is the exception).
3. Current user must `hasPermission( 'p_<package>_admin' )`.
4. Template shape used by existing packages:

```smarty
{strip}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.EXAMPLE_PKG_URL}admin/settings.php">{tr}Example Settings{/tr}</a></li>
</ul>
{/strip}
```

`$packageMenuClass` is `dropdown-menu sub-menu` in the top bar and `unstyled`
on the Kernel admin index. Do not hard-code the list class.

A **dedicated** `admin/*.php` page is the usual settings UI. Link it from
`menu_*_admin.tpl`. That is independent of Kernel’s `?page=` include (next).

### Kernel `admin/index.php?page=<name>`

If `?page=example` is used:

- Permission: `p_example_admin`.
- Include file: `<EXAMPLE_PKG_ADMIN_PATH>admin_example_inc.php`
  (special cases remap `page` → package in `kernel/admin/index.php`).

Packages that only have a custom `admin/settings.php` do **not** need
`admin_<pkg>_inc.php` unless they want the Kernel settings include. The
dropdown still works from `menu_*_admin.tpl` alone.

---

## Permissions

Table `users_permissions`: `perm_name`, `perm_desc`, `perm_level`, `package`.
`perm_name` is `varchar(30)`.

Green-field: `$gBitInstaller->registerUserPermissions( PKG, array(
	array( 'p_example_admin', 'Can admin the example package', 'admin', PKG ),
) );` in `admin/schema_inc.php`.

Existing site DBs: idempotent SQL, not a one-off `.sh` wrapper:

```sql
INSERT INTO users_permissions (perm_name, perm_desc, perm_level, package)
SELECT 'p_example_admin', 'Can admin the example package', 'admin', 'example'
WHERE NOT EXISTS (SELECT 1 FROM users_permissions WHERE perm_name = 'p_example_admin');
```

`BitPermUser::hasPermission()`: if `isAdmin()` (`p_admin` in `mPerms`),
**every** permission check returns true. Site admins therefore see every
package’s admin menu even when `p_<pkg>_admin` is missing from
`users_permissions` / group maps. Still register the row so Groups UI and
non-`p_admin` package admins work.

Package admin pages should `verifyPermission( 'p_<pkg>_admin' )`, not only
`p_admin`.

---

## Schema: installer vs live sites

Never assume live schema matches `admin/schema_inc.php`. Deployed DBs lag.

| Mechanism | When |
|---|---|
| `admin/schema_inc.php` (`registerSchemaTable`, `registerSchemaDefault`, `registerPackageInfo`) | Green-field installer |
| `admin/upgrades/<version>.php` + `registerPackageUpgrade` | Kernel package upgrader |
| Idempotent `admin/install_*.sql` applied with `psql -f` | Existing site DBs (typical operations) |

SQL files:

- Explicit `BEGIN` / `COMMIT` (client autocommit is often off).
- `CREATE TABLE IF NOT EXISTS` / `CREATE SEQUENCE IF NOT EXISTS` /
  `CREATE INDEX IF NOT EXISTS`.
- **`CREATE TABLE IF NOT EXISTS` does not add columns** to a table that
  already exists. Follow with `ALTER TABLE … ADD COLUMN IF NOT EXISTS`.
- Constraint adds wrapped in `DO $$ … IF NOT EXISTS (information_schema) …`.
- One pack file that a site which stopped at an earlier version can run once
  is better than a stack of tiny version files plus copy-paste `.sh`
  wrappers. Operators run `psql -v ON_ERROR_STOP=1 -h … -f admin/install_X.sql`.
- A `kernel_config` version key (`package_<pkg>_version`) is a useful pack
  marker; bump it at the end of the pack.

PostgreSQL is not required by upstream Kernel, but this install uses it
exclusively — keep `IF NOT EXISTS` / `ADD COLUMN IF NOT EXISTS` portable or
document the PG-only pack.

---

## Templates, CSS, shared alerts

- Resource: `bitpackage:<package>/<file>.tpl`.
- Shared includes for chrome that every package page needs (banners, heads).
  Assign flags from one PHP helper called by every controller (including
  admin) so a template `{if}` is not fighting missing vars.
- Load package CSS with **`PKG_PATH`**, not `PKG_URL`. BitThemes appends
  `?filemtime` only on the path form. URL-only loads stick behind long-lived
  proxy / PageSpeed caches.

---

## Config

`getConfig( $name, $default )`. A stored **empty** value is not the same as
missing; `if( !$gBitSystem->getConfig( $name ) )` treats `''` as missing.
`storeConfig( $name, $value, $package )` — always pass the owning package.
Per-request presentation overrides: `setRequestConfig()`, not `setConfig()`
(APCu singleton; see core-runtime.md).

Feature flags that change customer-visible behavior (simulate, seed, …)
belong in `kernel_config` and should be obvious in the UI when on.

---

## Docs for the package itself

`$WORK_ROOT/<pkg>/includes/docs/README.md` is the session-start index.
Put request/URL surface, ownership, and “how to verify without a browser”
there. Install-only overlay: `config/includes/docs/deployment.md`. Agent
credentials: `$DEV_ROOT` only (never `includes/docs/`).

---

## Open (for the fleshing-out agent)

- [ ] Full constant list from `registerPackage()` (`_PKG_URI`, `_PKG_TITLE`, …).
- [ ] `homeable` / package as site home.
- [ ] Liberty content types and `registerService` (only a pointer here).
- [ ] Module / layout registration.
- [ ] Pretty-URL `.htaccess` vs Kernel rewrite.
- [ ] Icon `pkg_<name>` used on the Kernel admin index panel heading.
- [ ] Test / lint conventions beyond “PHP lint + `$gBitSmarty->fetch()`”.
- [ ] Worked example: minimal package that appears in Administration.
