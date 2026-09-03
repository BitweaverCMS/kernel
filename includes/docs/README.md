# Kernel package documentation

> Engineering documentation derived from the source in this package. The
> package's `includes/` directory must be denied to direct HTTP requests.

## Purpose

Kernel bootstraps every Bitweaver request and coordinates configuration, packages, database access, and global services.

## Responsibility

Owns root-path discovery, setup order, package registration, system configuration, error handling, and core base classes.

## Dependencies

ADOdb and Smarty are external runtime dependencies; all Bitweaver packages depend on kernel..

Dependency direction matters: this package may depend on the packages above;
the dependencies do not thereby depend on this package.

## Boundary

Does not own content semantics, users, or presentation-specific business rules.

## APCu / layout-body gotcha

With `BIT_CACHE_OBJECTS`, `BitSystem` is an APCu-cached singleton and `mConfig`
is serialized into it. Each PHP-FPM worker has its own APCu, so a poisoned
config looks intermittent.

`kernel/templates/html.tpl` renders the main section as
`container{$gBitSystem->getConfig('layout-body')}`. An empty `layout-body`
(normal for Presto Photo) yields Bootstrap `container`; `-fluid` yields
full-width `container-fluid`.

**Do not** use `setConfig()` or direct `$gBitSystem->mConfig[...]` writes for
per-request layout overrides. Use `setRequestConfig('layout-body', '-fluid')`
so the override is visible to `getConfig()` for this request only and is never
written back to APCu. Clearing APCu (or a browser Shift+Reload that sends
`Cache-Control: no-cache`, which disables object cache for that request) is how
operators clear an already-poisoned worker.

Details: [core-runtime.md](core-runtime.md) (Configuration).

## Documentation map

- [Agent development guide](bitweaver.md) — generic deployment selection,
  safety, planning, and package-documentation loading protocol.
- [Architecture](architecture.md) — initialization, components, and request flow.
- [Source reference](source-reference.md) — source-derived files, classes,
  controllers, schema artifacts, plugins, and templates.
- [Development guide](development.md) — safe change workflow, extension points,
  validation, and maintenance guidance.
- [Security](security.md) — trust boundaries and direct-HTTP access requirements.
- [Package documentation plan](package-documentation-plan.md) — standard,
  review checklist, and continuation procedure for packages on other hosts.
- [Core runtime reference](core-runtime.md) — bootstrap phases, global objects,
  package discovery, configuration, database abstraction, and rendering.
