# Bitweaver package documentation plan

## Purpose

This plan defines how future agents document Bitweaver packages that are not
present in the current checkout. Documentation follows each package under
`includes/docs/` and is maintained with the source it describes.

It applies to upstream Bitweaver packages. Site-specific extensions must be
documented in their own repositories and must never be introduced as
dependencies or concepts in upstream package documentation.

## Required security prerequisite

Do not place internal engineering documentation beneath a public document root
until direct access to the entire package `includes/` subtree is denied.

Every package should contain:

- `includes/.htaccess` — recursive Apache 2.2/2.4 denial, including explicit
  protection for `.htaccess` and `web.config`.
- `includes/web.config` — recursive IIS authorization denial.

Nginx and Caddy require site-level URI rules because they ignore directory-local
files. See any package's `includes/docs/security.md` for canonical examples.

Verification is mandatory:

1. Request a known `includes/` PHP file.
2. Request `includes/web.config`.
3. Request `includes/docs/README.md`.
4. Require 403 or 404 for all three.

PHP 500 is a failure: it proves direct execution was attempted.

## Standard files

Every package receives:

| File | Purpose |
|---|---|
| `README.md` | Purpose, ownership, dependencies, boundaries, and doc index |
| `architecture.md` | Bootstrap, components, request flow, persistence, rendering |
| `source-reference.md` | Source-derived classes, controllers, schema, plugins, templates |
| `development.md` | Safe change workflow, conventions, testing, doc maintenance |
| `security.md` | Trust boundaries, permissions, data exposure, HTTP denial |

Add focused files when the package warrants them:

- `data-model.md`
- `api.md`
- `content-lifecycle.md`
- `permissions.md`
- `services-and-plugins.md`
- `workflows.md`
- `integrations.md`
- `operations.md`
- `troubleshooting.md`
- `testing.md`
- `migration.md`

Do not add an empty standardized file merely for symmetry. Link every focused
file from `README.md`.

## Evidence hierarchy

Use evidence in this order:

1. Current package source.
2. Current package schema and upgrade scripts.
3. Parent classes and called package APIs.
4. Tests and executable examples.
5. Version history when intent is unclear.
6. Official Bitweaver technical/developer documentation as historical context.

The source is authoritative for current behavior. Historical documentation can
explain design intent but may describe obsolete filenames, APIs, database
behavior, or PHP versions.

Never infer the exact production schema from a base `schema_inc.php`; deployed
instances may be at different migration levels.

## Upstream purity rule

Before completing an upstream package:

- Search its new docs for site, customer, deployment, and proprietary package
  names.
- Describe only dependencies that belong to upstream Bitweaver or external
  libraries/services intrinsic to the package.
- Do not document reverse dependencies from extensions.
- Use generic examples when the source checkout contains local integration
  patches.

Dependency direction must be explicit: an extension depending on an upstream
package never makes the upstream package depend on the extension.

## Documentation procedure

### 1. Establish package identity

Record:

- Repository and checkout directory names.
- Package name registered with `BitSystem`.
- Required versus optional status.
- Content type GUIDs.
- Service GUIDs provided or consumed.
- Core and optional dependencies.

Directory name and package identity can differ.

### 2. Inventory source

Exclude `.git`, generated files, caches, user storage, vendored dependencies,
and minified assets where possible.

Inventory:

- `includes/bit_setup_inc.php`
- Controllers and included controller fragments.
- First-party classes, interfaces, and traits.
- `admin/schema_inc.php`, upgrade files, and cleanup.
- Templates and modules.
- Liberty and Smarty plugins.
- AJAX, API, webhook, cron, CLI, and callback entry points.
- Tests, fixtures, and operational scripts.

Distinguish a directly callable controller from a library file by reading its
bootstrap and call sites, not by filename alone.

### 3. Trace initialization

Document:

- Package registration.
- Constants and path definitions.
- Singleton/global creation.
- Required includes.
- Content-type and service registration.
- Template assignments.
- Configuration-dependent branches.

### 4. Trace primary workflows

For each user- or system-visible workflow, follow:

```text
request/event
  → controller or callback
  → authentication and authorization
  → object construction/load
  → validation
  → persistence/service calls
  → rendering/response
  → cache, queue, or external side effects
```

Document failure paths and retry/idempotency behavior where relevant.

### 5. Document persistence

For every first-party table record:

- Purpose and owning class.
- Primary identity.
- Important foreign keys.
- Status/type discriminator fields.
- Creation/update/delete behavior.
- Relevant sequences, indexes, and constraints.
- Migration/upgrade caveats.

Avoid copying an entire schema without explaining relationships and ownership.

### 6. Document public extension contracts

Record:

- Base classes intended for inheritance.
- Service callbacks.
- Plugin registration hashes.
- Stable constants and GUIDs.
- Parameter-hash keys passed by reference.
- Return values and error conventions.
- Template variables used as cross-package contracts.

Do not label every public PHP method as a supported API. State whether a
contract is established, internal, legacy, or inferred.

### 7. Security review

Cover:

- Global and object permissions.
- Ownership and status.
- CSRF/challenge checks.
- Input sources and validation.
- SQL binding.
- File/path handling.
- Upload and MIME validation.
- HTML parsing/purification.
- Secrets and external credentials.
- Webhook/API authentication.
- Data exposed through lists, errors, logs, and caches.

### 8. Validate

- Cross-check documented paths with the checkout.
- Check Markdown links.
- Search upstream docs for proprietary leakage.
- Confirm no source file changed.
- Confirm HTTP requests to docs return 403/404.
- Review `git diff --check`.
- Record source revision and any dirty working-tree caveat during review, not
  as a permanent claim that becomes stale.

## Quality bar

A package is not fully documented merely because it has a file list. A future
agent should be able to answer:

- Why does the package exist?
- What does it own, and what does it deliberately not own?
- How is it initialized?
- Which objects and tables are authoritative?
- What are its primary request and background workflows?
- How are permissions enforced?
- Where can another package extend it?
- Which files must be changed together?
- How is a change tested and operated safely?
- What legacy behavior is easy to break?

## Recommended work order

1. Kernel, Liberty, Users, Themes, Languages, Util.
2. Content packages that subclass Liberty.
3. Cross-cutting Liberty service packages.
4. Packages with external services or background processing.
5. Presentation-only and compatibility packages.

Load the documentation for dependencies before documenting a dependent package.

## Official historical references

- [Technical Documentation table of contents](https://www.bitweaver.org/wiki/Technical%2BDocumentation)
- [Bitweaver Framework](https://www.bitweaver.org/wiki/Bitweaver%2BFramework)
- [Sample Package Dissection](https://www.bitweaver.org/wiki/SamplePackageDissection)
- [Liberty Services](https://www.bitweaver.org/wiki/LibertyServices)
- [Liberty Formats](https://www.bitweaver.org/wiki/LibertyFormats)
- [Liberty Content Permissioning](https://www.bitweaver.org/wiki/LibertyContentPermissioning)
- [API Documentation conventions](https://www.bitweaver.org/wiki/APIDocumentation)
