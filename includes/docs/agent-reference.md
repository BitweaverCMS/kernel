# Bitweaver agent reference

> Companion to [bitweaver.md](bitweaver.md). The protocol file keeps the rules
> and the startup steps; this file holds the material an agent needs only in a
> particular situation. Open the section the task is about.

---

## Interactive deployment discovery

Used when the first message is not in launcher format (no `source=`).

### Step 1 — Discover available deployments

Run:

```bash
ls $DEPLOY_BASE/*/kernel/includes/setup_inc.php 2>/dev/null \
  | sed "s|$DEPLOY_BASE/||; s|/kernel/includes/setup_inc.php||" | sort
```

Present the list to the user and ask which deployment to use.

### Step 2 — Confirm selection

> "Which deployment will we be working in?
> Discovered: **[list]**"
Then continue at Step 2b in [bitweaver.md](bitweaver.md).

---

## All Upstream Packages

```
blogs       bnspell     calendar    downloads (treasury)
feed        forums (boards)         gatekeeper  kernel
languages   liberty     messages    newsletters
nexus       photos (fisheye)        pigeonholes quicktags
quota       rss         search (ilike)           sharethis
smileys     stars       stats       tags
themes      users       util        wiki
```

Each lives in its own subdirectory and git submodule. When working on an
optional package, always check for dependencies on core packages.

---

## Optional packages (delete to disable)

Most feature packages can simply be deleted. Remove the package directory
(and its supermodule entry); the rest of the site should keep running.
Kernel and other packages must fail gracefully when an optional package is
absent — no installer, registry cleanup, or uninstall hook is required.
Package tables and rows stay in the database until someone drops them by
hand.

**Exceptions:** core packages (kernel, liberty, users, themes, languages,
util) and packages that others declare as hard dependencies. Those are not
optional; deleting them is not a supported way to turn a feature off.

---
---

## Creating a New Package

Each package is its **own git repository**, wired into a deployment as a **git
submodule** (the deployment checkout is the supermodule; the full set is recorded
in the deployment's `.gitmodules`). A package's directory name may differ from its
package name — the site overlay keeps that mapping.

To scaffold a brand-new package — directory layout, the required
`includes/bit_setup_inc.php` registration, schema/permissions, the `index.php`
view controller + `.htaccess` routing, dependency reuse, and the submodule +
activation steps — follow the dedicated reference:

> **`$WORK_ROOT/kernel/includes/docs/package-documentation-plan.md`** — Creating a New Bitweaver Package (patterns + checklist).

This is distinct from *Initialising a new package **workspace*** (below), which
only creates this developer's `plans/notes/files/memory` scratch dirs for an
**existing** package.

---

## Directory structure

### Deployment documentation

```
$WORK_ROOT/
  kernel/includes/docs/bitweaver.md       Generic instructions (this file)
  config/includes/docs/deployment.md      Optional installation overlay
  <package>/includes/docs/README.md       Package purpose and boundary
  <package>/includes/docs/toc.md          One line per package document
```

### Developer workspace

```
$DEV_ROOT/                     Personal workspace — never deployed
  agent bootstrap instructions   Select deployment and load deployment docs
  core/
    plans/  notes/  files/  memory/
  <package>/
    plans/                     One file per feature or fix
    notes/                     Scratch: research, traces, ideas
    files/                     Artefacts: SQL snippets, diffs, sample data
    memory/                    Local-only ephemeral context
```

Installation overlays may define additional developer-workspace paths (for
example a sessions index). Those belong in
`$WORK_ROOT/config/includes/docs/deployment.md`, not in this generic guide.

### Initialising a new package workspace

> This creates only the developer's scratch dirs for an **existing** package. To
> create a new Bitweaver **code package** (its own submodule, `bit_setup_inc.php`,
> schema, controllers), see `$WORK_ROOT/kernel/includes/docs/package-documentation-plan.md`.

If `$DEV_ROOT/<package>/` does not exist, propose:

```bash
mkdir -p $DEV_ROOT/<package>/{plans,notes,files,memory}
```

Show the command; wait for confirmation before running.

---

## Admin Mode

Active when `$DEV_ROOT` is not set (an agent invoked directly from an arch repo
directory, no developer bootstrap). In admin mode the agent can browse and update
arch docs, and initialise new developer workspaces. Package workspace
operations are not available.

### Creating a new developer workspace

When asked to create a workspace (e.g. *"create developer workspace named
spider.md"*):

1. Verify the target directory does not already exist.
2. Present this batch and ask for **one confirmation**:
   ```
   mkdir <target>
   git init <target>
   create the coding agent’s bootstrap instructions from its repository template
       (fill {{DEVNAME}}, {{DEV_ROOT}}, {{DEPLOY_BASE}})
   mkdir -p <target>/core/{plans,notes,files,memory}
   ```
3. Execute on confirmation.
4. Report: *"Workspace ready at `<target>`. Developer starts their coding agent from that directory."*

---
---

## Plan File Format

```markdown
# <Package> — <Feature or Fix Title>

## Status
<!-- draft | active | blocked | complete -->

## Scope
<!-- what this covers and explicitly what it does NOT cover -->

## Dependencies
<!-- other packages or plans this depends on -->

## Approach
<!-- numbered steps -->

## Open Questions
<!-- needs user input or research before proceeding -->

## Decisions Log
### YYYY-MM-DD — <short title>
<rationale>

## Change History
### YYYY-MM-DD — <short title>
Files: <list>
Summary: <what changed and why>
```

Rules:
- Update plan files only after a change is applied and confirmed.
- Never update speculatively.
- Name descriptively: `feature-wiki-edit-permissions.md`, not `plan1.md`.

---
---

## Analysis Output Format

```
### Problem
<plain-language description>

### Root Cause
<file(s), function(s), query responsible — and why>

### Affected Packages
<list — always include core packages if impacted>

### Proposed Fix
<what changes and why it is safe>

### Files to Change
- path/to/file.php  — <one-line reason>

### Diff Preview
<shown after user says "show diff" or "proceed">
```

---
---

## Useful References

- Bitweaver Overview: https://www.bitweaver.org/wiki/Bitweaver+Overview
- Framework & Package List: https://www.bitweaver.org/wiki/Bitweaver+Framework
- Supermodule repo: https://github.com/BitweaverCMS/bitweaver
- Individual package repos: https://github.com/bitweaver/<package>
- ADOdb docs: https://adodb.org/dokuwiki/doku.php
- Smarty docs: https://www.smarty.net/docs/en/
