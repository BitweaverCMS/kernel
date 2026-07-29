# Kernel core runtime reference

## Bootstrap contract

Normal web controllers enter Bitweaver through
`kernel/includes/setup_inc.php`. That file is orchestration, not a reusable
utility include. Its observed sequence is:

1. Resolve `BIT_ROOT_PATH` and load `config_defaults_inc.php`.
2. Load the error layer and shared Kernel functions.
3. Normalize request input and establish runtime defaults.
4. Load database configuration and create the database abstraction.
5. Load `BitSystem`; construct the system, Smarty, and Themes objects.
6. Check installed version/environment state.
7. Scan active package `includes/bit_setup_inc.php` files.
8. Apply local override hooks.
9. Finalize shared template assignments and request checks.

CLI tools may define the established command-line flags before setup. Do not
invent a second partial bootstrap: code that needs framework globals should use
the canonical setup path or a documented installer-specific bootstrap.

## Global objects

After successful setup, code commonly relies on:

| Global | Owner | Role |
|---|---|---|
| `$gBitSystem` | Kernel | Configuration, packages, output, features |
| `$gBitDb` | Kernel | Bound-query database abstraction |
| `$gBitSmarty` | Themes | Template assignment and rendering |
| `$gBitThemes` | Themes | Style, layout, assets, response format |
| `$gBitUser` | Users | Current identity and permissions |
| `$gBitLanguage` | Languages | Current language/translation state |
| `$gLibertySystem` | Liberty | Content types, services, plugins |

Availability depends on bootstrap phase. Package setup files must account for
load order and should use `isPackageActiveEarly()` when they must safely cause
another package bootstrap to load.

## Package discovery

`BitSystem::scanPackages()` searches package directories for a named scan file.
For normal setup that file is `includes/bit_setup_inc.php`. `loadPackage()`
loads it, and `registerPackage()` records the package.

A registration hash must provide `package_name` and `package_path`. It can also
declare required status and other metadata. Registration defines package path
and URL constants used throughout controllers and templates.

Installed, active, registered, and loaded are different states:

- **Installed**: schema/version state exists.
- **Active**: enabled by configuration.
- **Registered**: represented in `BitSystem::mPackages`.
- **Loaded**: the relevant scan file executed in this request.

Use `isPackageInstalled()`, `isPackageActive()`,
`isPackageActiveEarly()`, and `verifyPackage()` according to the question
being asked.

## Configuration

`BitSystem::loadConfig()` loads package preferences into runtime state.
`getConfig()` reads with a default; `setConfig()` changes in-memory state;
`storeConfig()` persists. Match helpers operate on groups of names.

Always pass the owning package when persisting a package preference. Avoid
using configuration as transient request state. A missing key and a stored
empty value can have different semantics, so preserve established defaults.

## Database abstraction

`BitDb` is the contract; `BitDbAdodb` is the primary adapter in this checkout.
Use `query()`, `getRow()`, `getOne()`, `getAll()`, `getAssoc()`, and `getCol()`
with bind arrays. `GenID()` abstracts sequence allocation.

Transaction methods are `StartTrans()`, `CompleteTrans()`, and
`RollbackTrans()`. Confirm nested/failed transaction behavior before composing
operations across packages.

`convertQuery()` and helpers such as `ifNull()`, `SQLDate()`, and `substr()`
exist to preserve database portability. Do not introduce raw PDO/mysqli calls
or concatenate request values into SQL.

## Rendering and response

`BitSystem::display()` is the main HTML response path:

1. `preDisplay()` loads layout and style state.
2. The center template is assigned.
3. Themes selects response format/display mode.
4. The outer Kernel HTML template renders the page.
5. `postDisplay()` completes response work.

`outputJson()` and `outputRaw()` are alternate response paths. Set meaningful
HTTP status codes and do not emit output before headers.

`fatalError()` and `fatalPermission()` integrate errors with HTML/AJAX
presentation. Avoid leaking SQL, paths, credentials, or protected object data.

## Base classes

- `BitBase` — shared validation, field, cache, and utility behavior.
- `BitSingleton` — singleton lifecycle used by system registries/managers.
- `BitSystem` — runtime coordinator and installer registry surface.
- `BitDb` / `BitDbAdodb` — database abstraction.
- `BitTimer` — named request timers.
- `BitCliArgs` — command-line option parsing.
- `HttpStatusCodes` — response code/message helpers.

## Files that change together

- Bootstrap changes: `setup_inc.php`, defaults, involved singleton classes, and
  required-package setup files.
- New configuration: schema preference registration, admin UI, runtime default,
  and documentation.
- New package metadata: package setup, schema registration, and installer flow.
- New output mode: `BitSystem`, Themes format handling, templates, and tests.
