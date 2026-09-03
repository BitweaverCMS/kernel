# Bitweaver agent development guide

> Generic session instructions for developing on [Bitweaver CMS](https://www.bitweaver.org/wiki/Bitweaver+Overview).
> This is the generic instruction layer shipped with Kernel. A launcher or
> developer workspace sets the deployment paths before this file is read.
>
> This file is suitable for publication and is intended to be shared as a
> starting point for any team developing on Bitweaver with coding agents.
>
> Installation-specific overlays belong in
> `$WORK_ROOT/config/includes/docs/deployment.md` and load after this file.

---

## Path Variables

All path references in this file use these variables. They must be set by
the developer bootstrap before this file is read.

| Variable | Purpose |
|----------|---------|
| `$WORK_ROOT` | Selected Bitweaver deployment/document root |
| `$DEV_ROOT` | This developer's personal workspace (plans, notes, files) |
| `$DEPLOY_BASE` | Parent directory of all deployment clones |

If `$DEV_ROOT` is not set, the agent is in **admin mode** — see that section below.

---

## ⚠️ CRITICAL OPERATING RULES

Non-negotiable. Apply in every session.

0. **Session startup runs first, always.** Before responding to any first
   message — regardless of what it says — the agent must complete the Session
   Startup protocol below. No source file may be read until `$WORK_ROOT` is
   confirmed and architecture docs are loaded.

1. **No automatic changes.** The agent must never write, edit, delete, or rename
   any file without explicit user confirmation for that specific change.
   Exception: **trivial PHP notice/warning one-liners** — apply immediately per
   Planning Workflow, then report; do not wait for approval.

2. **No automatic commits.** Run `git commit` only when the current prompt
   explicitly requests a commit or invokes Session Closeout. Never `git push`,
   merge, rebase, or perform a destructive Git operation unless the user asks
   for that exact operation.

3. **Analyse first, act second.** For every problem:
   - Identify root cause and affected files/packages.
   - Propose a clearly described solution.
   - Wait for the user to say "implement" (or equivalent) before touching any file.

4. **Show diffs before applying.** Display the exact diff and ask for final
   confirmation before writing any change. Exception: **trivial PHP
   notice/warning one-liners** may be applied immediately — see Planning
   Workflow — then report what changed. Do not ask for approval on those.

5. **One change at a time.** Each logical change is confirmed individually
   unless the user explicitly approves a batch.

6. **No package manager side-effects.** Do not run `composer install/update`,
   `npm install`, or equivalent without confirmation.

7. **Architectural discoveries follow the package.** When the agent confirms a
   convention, data model detail, gotcha, or any fact that all developers
   should know, it proposes an addition to `$WORK_ROOT/<package>/includes/docs/README.md`.
   These belong in the shared repo, not in a developer-local `memory/` file.

8. **Never search the `storage` module.** Do not run `ugrep`/`grep`/`rg`/
   `find`/glob (or any recursive scan) inside the **storage** package
   directory (`storage/`, e.g. `$WORK_ROOT/storage/`), or any directory named
   `storage`. It holds millions of generated/user files (rendered pages,
   thumbnails, uploads, caches); `storage/users/` and `storage/*/users/`
   specifically contain large volumes of user-uploaded assets. Crawling it
   causes **severe CPU and disk-I/O spikes** on the server. Always exclude it: use `--exclude-dir=storage`
   (grep), `-g '!storage'`/`--ignore-dir=storage` (rg/ugrep), or
   `-not -path '*/storage/*'` (find). To reach a specific generated file,
   **derive its path** from the storage-branch convention instead of scanning.

9. **Never delete files in `storage/`.** Files under any `storage/` directory
   must never be deleted by the agent under any circumstances. The user must
   delete storage files manually.

10. **Never delete any file without explicit user approval.** Propose the
    deletion and wait for the user to confirm before running any `rm` or
    equivalent command.

11. **Never run `sudo` without explicit per-command user confirmation.** Before
    issuing any `sudo` command, state the exact command and its effect, and
    wait for the user to approve that specific invocation. This applies even
    when a task obviously requires elevated privileges — ask first, every time.

---

## Project Overview

Bitweaver is a modular, object-oriented PHP CMS using **Smarty templates** and
**ADOdb** for database abstraction. It supports MySQL, PostgreSQL, SQLite,
Firebird, Oracle, MS SQL Server, and Sybase.

The upstream repository at `https://github.com/BitweaverCMS/bitweaver` is a
**git supermodule**; each package is a git submodule tracked independently
under `https://github.com/bitweaver/<package>`.

---

## Core Packages (always in scope)

| Package | Role |
|---------|------|
| **kernel** | Database initialisation, package configuration, bootstrap |
| **liberty** | Base content classes — access control, history, formatting, wiki parsing, HTML scrubbing |
| **users** | User data, authentication, session management |
| **themes** | Site theming, Smarty template resolution |
| **languages** | Internationalisation (i18n) |
| **util** | Shared utility functions used across packages |

Reference: https://www.bitweaver.org/wiki/Bitweaver+Framework

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

## Session Startup

**Complete this protocol before responding to any first message.**

### Step 0 — Pre-fill detection

Check whether the first user message is in launcher format (sent by the
`bwdev` shell function):

- **`source=<name>`** — treat `<name>` as the pre-selected deployment; skip
  Steps 1–2 and go directly to Step 2b validation.
- **`source=<name> package=<name>`** — additionally pre-select the package;
  skip the package prompt in Step 4b.

If neither pattern is present, proceed with interactive Steps 1–2.

### Step 0b — Admin mode detection

If `$DEV_ROOT` is not set (no developer bootstrap was loaded), enter
**admin mode**. Skip Steps 1–4. See the Admin Mode section.

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

### Step 2b — Validate

```bash
ls $DEPLOY_BASE/<name>/kernel/includes/setup_inc.php
```

If absent, report the error and re-prompt. Do not proceed with an invalid
deployment.

If the site-specific overlay (`$WORK_ROOT/config/includes/docs/deployment.md`) defines any
environment-specific confirmation rules for the selected deployment (e.g.
requiring explicit confirmation for a production environment), apply them
now before setting `$WORK_ROOT`.

### Step 3 — Confirm working root

Set `$WORK_ROOT` = `$DEPLOY_BASE/<chosen>/` and echo:

> "Working root confirmed: `$DEPLOY_BASE/<chosen>/`"

Do not read or modify files outside `$WORK_ROOT` or `$DEV_ROOT`.

### Step 4 — Load core architecture

Read `$WORK_ROOT/kernel/includes/docs/core-runtime.md`. This documents the framework globals,
inheritance chain, LibertyContent polymorphism, BitPermUser, BitSystem,
and template resolution. Required every session regardless of package.

### Step 4b — Select package and load package architecture

Ask: *"Which package will we be focusing on this session?"*
(Skip if package was pre-filled in Step 0.)

Once confirmed:

1. Resolve package identity to its checkout directory when they differ.
2. Read `$WORK_ROOT/<directory>/includes/docs/README.md` if it exists.
3. Follow the README links needed for the task.
4. Check `$DEV_ROOT/<directory>/plans/` for active plans and report what's found.

Confirm readiness:

> "Ready. Loaded: `$WORK_ROOT` | package: `<package>` | arch: [files loaded] | plans: [N found]
> What are we working on?"

---

## Session Workflow

```
1.  Session startup completes (Steps 0–4b above).
2.  The agent enters the planning workflow — confirm scope before any code work.
3.  The agent reads only the source files necessary for the task.
4.  The agent analyses and proposes a solution in plain language.
5.  User approves or redirects.
6.  The agent shows the exact diff.
7.  User confirms.
8.  The agent applies the change.
9.  The agent updates the plan file in $DEV_ROOT/<package>/plans/.
10. If the change surfaces a shared architectural fact, the agent proposes an
    addition to $WORK_ROOT/<package>/includes/docs/README.md.
11. The agent validates the change and reports any unverified behavior.
12. The agent commits only on an explicit commit request or Session Closeout.
```

---

## Session Closeout

The phrases **wrap up**, **session closeout**, **end session**, and equivalent
end-of-work language invoke this procedure and count as an explicit request to
commit verified session work. They do not authorize a push.

1. Inventory every dirty owning repository with `git status` and `git diff`.
2. Separate this session's paths and hunks from human or other-agent work.
3. Finish plan, package documentation, and durable handoff updates first.
   Apply any Session Closeout additions from the installation overlay
   (`$WORK_ROOT/config/includes/docs/deployment.md`) when that file defines them.
4. Run validation appropriate to the change: relevant package tests when
   available, PHP lint for changed PHP, focused request/render checks for
   controllers or Smarty changes, schema checks for migrations, and Markdown
   link/security checks for documentation.
5. State exactly what remains unverified. Do not describe unvalidated behavior
   as complete. If a requested commit would include unverified work, stop and
   let the user decide whether to commit it.
6. Stage verified session paths explicitly, inspect `git diff --cached`, and
   commit in each owning package repository with a focused message.
7. Report commit hashes, validation performed, uncommitted foreign/residual
   paths, dirty parent submodule pointers, and confirmation that nothing was
   pushed.

Prefer committing verified work and documenting residual gaps over leaving
verified session work dirty after closeout. Closeout is not permission to stage
the entire worktree.

---

## Planning Workflow

**The planning workflow must be active before beginning any feature work or bug fix.**
A named, active plan file must exist before any code is read or changed.

### Starting a plan

1. Check `$DEV_ROOT/<package>/plans/` for an existing matching plan.
2. If found:
   > "Found existing plan: `<filename>` — scope: <summary>.
   > Is this the plan we are working from, or do you want a new one?"
3. If not found:
   > "No existing plan for this. Describe the full scope so I can draft
   > one for your review before we begin."
4. Draft presented; no code touched until user approves.
5. On approval:
   > "Plan active — working from: `<filename>`"

### Ambiguous scope

> "Is this a standalone fix, part of a larger feature, or an ad-hoc change?
> Should I create a plan, add to an existing one, or proceed without one?"

### Trivial fixes (PHP notices / warnings / one-liners)

Trivial one-liners may proceed without a plan file and **without waiting for
approval**. When the user pastes a PHP `NOTICE` / `WARNING` / `ERROR` (or
similar) whose fix is a small, local guard — undefined array key or variable,
null check, wrong variable name, obvious arity/typo — use this fast path:

1. State explicitly: **"Trivial fix, no plan."**
2. Give a short root cause (file, line, why).
3. Apply the one-line (or equivalently tiny) fix immediately.
4. Report what changed (the exact edit is enough; no approval prompt).

Do **not** add process overhead that the pasted warning does not need:

- no plan-or-not multiple-choice menus
- no deployment/package retarget quizzes when the stack trace already names the
  file (note any `$WORK_ROOT` mismatch in one line and continue)
- no "implement?" / "apply?" confirmation for the trivial edit itself

Still required: no automatic commits, and analyse before editing. If the
diagnosis shows the fix is broader than a local guard, leave the fast path and
use the normal planning workflow (propose, confirm, then write).

---

## Directory Structure

### Deployment documentation

```
$WORK_ROOT/
  kernel/includes/docs/bitweaver.md       Generic instructions (this file)
  config/includes/docs/deployment.md      Optional installation overlay
  <package>/includes/docs/README.md        Package documentation index
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

### What goes where

| Location | Purpose |
|----------|---------|
| `$WORK_ROOT/kernel/includes/docs/bitweaver.md` | Generic development protocol |
| `$WORK_ROOT/config/includes/docs/deployment.md` | Optional installation-specific rules |
| `$WORK_ROOT/<pkg>/includes/docs/README.md` | Package facts and documentation map |
| `$DEV_ROOT/<pkg>/plans/` | This developer's active and archived plans |
| `$DEV_ROOT/<pkg>/notes/` | Scratch research, error traces, ideas |
| `$DEV_ROOT/<pkg>/files/` | SQL snippets, example data, diffs |
| `$DEV_ROOT/<pkg>/memory/` | Developer-local context only — not shared |

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

## Code Conventions

- **PHP style**: follow existing file conventions (procedural + OOP hybrid;
  classes in `<Package>Lib.php`).
- **Templates**: Smarty `.tpl` files in each package's `templates/`; never
  embed logic in templates.
- **Database**: always use the ADOdb abstraction layer (`$gBitDb`); never
  write raw PDO or mysqli calls.
- **Permissions**: use the Liberty/kernel permission API; never bypass ACL.
- **i18n**: all user-facing strings wrapped in `tra()`.
- **HTML**: templates must be valid HTML5.

---

## Commit and Git Safety Policy

```bash
# Run only on explicit request or Session Closeout:
git commit

# NEVER run automatically:
git push
git merge
git rebase
git reset --hard
git clean -fd

# Safe read-only operations:
git status
git diff
git log --oneline -n 20
git show <sha>
git branch -a
git submodule status
```

### Commit authorization

- Never commit merely to checkpoint or save progress. Untested or unverified
  history creates work for every later developer and agent.
- A direct commit request or Session Closeout authorizes commits only after the
  validation and ownership checks below. It never authorizes a push.
- If validation is incomplete, identify the exact gap and ask before committing
  the affected work. Verified independent work may still be committed.
- After several coherent, verified changes accumulate mid-session, the agent may
  suggest one focused commit message. A suggestion is not permission to commit.

### Shared-worktree ownership

Multiple humans and agents may share a deployment checkout. A commit request,
including closeout, is not permission to stage the whole dirty tree.

- Commit only paths and hunks created or intentionally edited by this agent for
  the current task. Plans and durable documentation written by this agent count
  as session work.
- Before staging, inspect `git status`, relevant diffs, and submodule status.
  Classify session-owned, foreign, and mixed paths.
- If any dirty path is foreign, or a session file contains mixed foreign hunks,
  stop and give the user a short path-and-reason inventory before staging.
- When the user directs a split, stage only the approved paths or hunks. Leave
  foreign work untouched and name it in the closeout handoff.
- A package commit and a deployment-superproject pointer update are separate
  commits. Do not stage a submodule pointer unless this session intentionally
  updates that deployment pin and the user-authorized scope includes it.

### Staging: explicit paths only

Deployment repos contain **symlinks to proprietary code** in other submodules.
`git add -A` and `git add .` will silently stage those symlinks and push
proprietary partner names to public GitHub mirrors.

Never use `git add -A`, `git add .`, `git commit -a`, or an unscoped
`git add -u`. Name every approved path explicitly; use patch staging only when
the user has approved a hunk split. Before committing, verify that every cached
path belongs to the intended repository and session scope.

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

## What Agents Will NOT Do

- Modify any file without explicit per-change confirmation.
- Run `git commit` without a direct commit request or Session Closeout.
- Run `git push` or another history-altering operation without an explicit
  request for that operation.
- Install or upgrade dependencies without confirmation.
- Assume the live database schema — ask the user for `SHOW TABLES` / `\d`
  output if schema knowledge is needed.
- Skip error handling or ACL checks in proposed code.
- Access or modify files outside `$WORK_ROOT` or `$DEV_ROOT`.
- Run `ugrep`/`grep`/`rg`/`find`/glob inside the `storage` module (or any
  `storage/` directory) — it spikes server CPU and disk. Always exclude it.
- Write shared architectural discoveries to `$DEV_ROOT/memory/` — these
  belong in `$WORK_ROOT/<package>/includes/docs/README.md`.

---

## Useful References

- Bitweaver Overview: https://www.bitweaver.org/wiki/Bitweaver+Overview
- Framework & Package List: https://www.bitweaver.org/wiki/Bitweaver+Framework
- Supermodule repo: https://github.com/BitweaverCMS/bitweaver
- Individual package repos: https://github.com/bitweaver/<package>
- ADOdb docs: https://adodb.org/dokuwiki/doku.php
- Smarty docs: https://www.smarty.net/docs/en/
