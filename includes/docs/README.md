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

Index: [toc.md](toc.md).
