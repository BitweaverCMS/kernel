# Bitweaver agent development guide

> Generic session protocol shipped with Kernel for developing on
> [Bitweaver CMS](https://www.bitweaver.org/wiki/Bitweaver+Overview) with a
> coding agent. A launcher or developer workspace sets the path variables
> before this file is read; an installation overlay at
> `$WORK_ROOT/config/includes/docs/deployment.md` loads after it. Suitable
> for publication as a starting point for any team using agents on Bitweaver.
>
> Situational material (interactive deployment discovery, the package list,
> workspace scaffolding, admin mode, plan and analysis formats) is in
> [agent-reference.md](agent-reference.md). Closeout and commit procedure is
> in [closeout.md](closeout.md). Open either only when the task is that concern.

## Path variables

| Variable | Purpose |
|---|---|
| `$WORK_ROOT` | Selected deployment / document root |
| `$DEV_ROOT` | This developer's workspace (plans, notes, files). Unset means admin mode (agent-reference.md) |
| `$DEPLOY_BASE` | Parent directory of all deployment clones |

## Critical operating rules

Non-negotiable, every session.

1. **Startup first.** Complete Session Startup before responding to any first message, whatever it says. No source file is read before `$WORK_ROOT` is confirmed and the documentation chain is loaded.
2. **Judge scope.** Small work (local, clear intent, one concern: a notice or deprecation, a null guard, a typo, a tight bugfix or enhancement, a short docs tweak) gets one analysis pass and is applied; no plan file, no "implement?" prompt. Large work (new feature, multi-package, schema or behavioural design, unclear approach) is never started unasked: ask once whether to draft a plan first or start implementing.
3. **Commits only on request.** `git commit` only when the prompt asks for it or invokes Session Closeout. Never `git push`, merge, rebase, `reset --hard`, or `clean` unless the user asks for that exact operation. Never commit to checkpoint.
4. **One logical change per edit, shown.** Small work is applied and reported. Large or plan-driven work shows the intended change and waits before writing.
5. **No package-manager side effects** (`composer`, `npm`, equivalents) without confirmation.
6. **Keep documentation current in the same turn.** A confirmed convention, data-model fact, gotcha, or documentation gap goes into the owning tree now, not at closeout and not as a "propose later" note. Package facts: `$WORK_ROOT/<package>/includes/docs/` (README is purpose and boundary, about a screen; `toc.md` is one line per document; procedures, formulas, and examples go in the linked file). Installation facts: `config/includes/docs/deployment.md` or a file it links. Credentials and agent access: `$DEV_ROOT` only. Write for the next agent's startup cost: record the discovery, not the search; a toc line that grows past one clause moves into its file; never leave a shared fact only in `$DEV_ROOT/memory/`.
7. **Never search `storage`.** No `grep`, `rg`, `ugrep`, `find`, glob, or recursive scan inside any `storage/` directory: it holds millions of generated and user files and a crawl spikes server CPU and I/O. Exclude it always (`--exclude-dir=storage`, `-g '!storage'`, `-not -path '*/storage/*'`). Derive a generated file's path from the storage-branch convention instead.
8. **Never delete in `storage/`.** Never delete any other file without explicit approval: propose the exact `rm`, wait.
9. **No `sudo` without per-command confirmation.** State the exact command and its effect, wait, every time, even when the task obviously needs it.
10. **No new work in a dirty package tree.** `git status` the owning package submodule (not the deployment supermodule) before the first write. Modified, staged, or untracked files that are not this task: stop, report the paths, wait. Ahead of or behind `origin` is not dirty; dirty supermodule pointers alone do not block a clean package.
11. **Authentication details never go in `includes/docs/`** (any package, Kernel, or `config/`, HTTP denial notwithstanding): no secrets, secret file paths, credential key or env-var names, auth accounts or logins. Public hostnames and DSN shapes (`https://<key>@host/<id>`) are fine. Agent auth lives in `$DEV_ROOT`.
12. **Public packages stay unforked and vendor-neutral.** `github.com/bitweaver/*` packages are shared by every install. Install behaviour goes in a `config/` include, a `config/themes/<package>/` overlay, a theme, or a private package, never in a public package tree, not even committed locally. Only generic fixes every install should have land in a public package. Public source, comments, docs, and commit messages name no installation brand, hostname, tracker id, or private (unpublished) package; rewrite such a message before `git push`. Detail: agent-reference.md.
13. **Never assume the live schema;** ask for `\d` or `SHOW TABLES` output. Never skip error handling or ACL checks in proposed code.
14. **Stay inside `$WORK_ROOT` and `$DEV_ROOT`.**

## Project overview

Bitweaver is a modular, object-oriented PHP CMS using Smarty templates and ADOdb (MySQL, PostgreSQL, SQLite, Firebird, Oracle, MS SQL Server, Sybase). Upstream `https://github.com/BitweaverCMS/bitweaver` is a git supermodule; each package is a submodule at `https://github.com/bitweaver/<package>`. Core packages always in scope: **kernel** (database init, package configuration, bootstrap), **liberty** (base content classes: ACL, history, formatting, wiki parsing, HTML scrubbing), **users**, **themes**, **languages**, **util**. Other packages, delete-to-disable, and the new-package checklist: agent-reference.md and `package-documentation-plan.md`.

## Session startup

Complete before responding to the first message.

1. **Pre-fill.** A launcher message carries `source=<name>` and usually `package=`, `work_root=`, and the documentation paths. Without `source=`, run interactive deployment discovery (agent-reference.md). Without `$DEV_ROOT`, enter admin mode (agent-reference.md) and skip the rest.
2. **Validate.** `ls $DEPLOY_BASE/<name>/kernel/includes/setup_inc.php`; if absent, report and re-prompt. Apply any deployment confirmation rule the installation overlay defines before setting `$WORK_ROOT`.
3. **Confirm.** Set `$WORK_ROOT` = `$DEPLOY_BASE/<name>/` and say: `Working root confirmed: <path>`.
4. **Package.** Ask which package when none was pre-filled. Resolve the package name to its checkout directory when they differ, read `$WORK_ROOT/<dir>/includes/docs/README.md` and `toc.md`, and list the file names in `$DEV_ROOT/<dir>/plans/` without opening them. Do not open a toc link, `core-runtime.md`, or any source file until the task is that concern.
5. **Ready.** `Ready. Loaded: $WORK_ROOT | package: <package> | arch: [files loaded] | plans: [N found]`, then ask what to work on.

## Session workflow

Judge scope (rule 2) and check the owning package tree (rule 10) before the first write. Read only the source the task needs.

**Small work:** say the scope in one line (`Small fix, no plan.`), analyse the files that set the reported behaviour in one pass, apply, report what changed and what was not verified. When the user is on the page and has named the element and the value, those files are the analysis: do not read the library that paints the widget, its history, or neighbouring features unless that pass fails to explain the symptom, and do not re-measure a value they reported. A pasted PHP notice whose stack names the file skips deployment or package retarget questions (note a `$WORK_ROOT` mismatch in one line). Startup already loaded the guides; do not re-read them. If analysis shows the work is large, switch.

**Large work:** check `$DEV_ROOT/<package>/plans/` for a matching plan and say so. Ask once: "This looks like a larger piece of work. Should I draft a plan first, or start implementing?" Draft only if asked, for review, no code until approved; on approval say `Plan active — working from: <filename>` and implement from it, showing each intended change first. Update a plan only after a change is applied and confirmed, never speculatively. Plan file format and the analysis layout (Problem / Root Cause / Affected Packages / Proposed Fix / Files to Change / Diff Preview) are in agent-reference.md.

Update the owning docs as facts surface (rule 6). Report unverified behaviour as unverified. **Wrap up**, **session closeout**, **end session**, or equivalent invokes [closeout.md](closeout.md): it authorises commits of verified session paths, staged explicitly, and never a push.

## Directory structure

| Location | Purpose |
|---|---|
| `$WORK_ROOT/kernel/includes/docs/bitweaver.md` | This protocol |
| `$WORK_ROOT/config/includes/docs/deployment.md` | Optional installation overlay |
| `$WORK_ROOT/<pkg>/includes/docs/README.md`, `toc.md` | Package boundary; one line per document |
| `$DEV_ROOT/<pkg>/plans/`, `notes/`, `files/`, `memory/` | Developer plans, scratch research, artefacts, local-only context |

Workspace scaffolding and the full tree: agent-reference.md.

## Code conventions

- Follow the file's existing style (procedural + OOP hybrid; classes in `<Package>Lib.php`). Shared helpers in `includes/<group>_lib.php`; `includes/classes/<ClassName>.php` when the unit needs instance state or inherits `BitBase`; request-handling utilities stay out of `*_inc.php` unless it is a controller include that runs in page scope.
- No superglobals in libraries. The controller (or CLI entry script) passes `$_POST` / `$_REQUEST` in; helpers take a parameter hash (`$pParamHash`) and read keys with `BitBase::getParameter( $pParamHash, 'key', $default )`.
- Smarty `.tpl` files in each package's `templates/`, no logic in templates, valid HTML5. All user-facing strings through `tra()`.
- Database through `$gBitDb` (ADOdb) only; never raw PDO or mysqli. Permissions through the Liberty / kernel ACL API only.

## Git safety

Read-only at any time: `git status`, `git diff`, `git log`, `git show`, `git branch -a`, `git submodule status`. `git commit` only per rule 3. Never `git push`, `git merge`, `git rebase`, `git reset --hard`, or `git clean -fd` unasked. Stage explicit paths only: never `git add -A`, `git add .`, `git commit -a`, or an unscoped `git add -u`, because deployment trees hold symlinks to proprietary code. Ownership checks and staging detail: closeout.md.
