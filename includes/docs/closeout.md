# Session closeout and commits

> Companion to [bitweaver.md](bitweaver.md). Open when the user says
> **wrap up**, **session closeout**, **end session**, or equivalent, or asks
> for a commit. Those phrases are an explicit request to commit verified
> session work. They never authorise a push.

## Procedure

1. Inventory every dirty owning repository with `git status` and `git diff`.
2. Separate this session's paths and hunks from human or other-agent work.
3. Finish plan, package documentation, and durable handoff updates first.
   Apply any closeout additions the installation overlay
   (`$WORK_ROOT/config/includes/docs/deployment.md`) defines.
4. Validate: package tests when available, `php -l` for changed PHP, the
   installation's fix check for controllers or Smarty changes, schema checks
   for migrations, Markdown link and secret checks for documentation.
5. State exactly what remains unverified. Do not describe unvalidated
   behaviour as complete. If a requested commit would include unverified
   work, stop and let the user decide.
6. Stage verified session paths explicitly, inspect `git diff --cached`, and
   commit in each owning package repository with a focused message.
7. Report commit hashes, validation performed, uncommitted foreign or
   residual paths, dirty parent submodule pointers, and that nothing was
   pushed.

Prefer committing verified work and documenting residual gaps over leaving
verified session work dirty. Closeout is not permission to stage the whole
worktree.

## Commit authorisation

- Never commit merely to checkpoint. Unverified history creates work for
  every later developer and agent.
- A direct commit request or closeout authorises commits only after the
  validation and ownership checks here. It never authorises a push.
- If validation is incomplete, name the exact gap and ask before committing
  the affected work. Verified independent work may still be committed.
- After several coherent, verified changes accumulate mid-session, the agent
  may suggest one focused commit message. A suggestion is not permission.

## Shared-worktree ownership

Several humans and agents may share a deployment checkout.

- Commit only paths and hunks this agent created or intentionally edited for
  the current task. Plans and durable docs written this session count.
- Before staging, inspect `git status`, the diffs, and `git submodule status`;
  classify session-owned, foreign, and mixed paths.
- Any foreign dirty path, or a session file with foreign hunks: stop and give
  a short path-and-reason inventory before staging. When the user directs a
  split, stage only the approved paths or hunks, leave the rest untouched,
  and name it in the handoff.
- A package commit and a deployment supermodule pointer update are separate
  commits. Do not stage a submodule pointer unless this session intentionally
  updates that pin and the user's authorised scope includes it.

## Staging: explicit paths only

Deployment repositories contain symlinks to proprietary code in other
submodules. `git add -A` and `git add .` silently stage those symlinks and
push proprietary names to public GitHub mirrors. Name every approved path
explicitly; use patch staging only when the user approved a hunk split.
Before committing, verify that every cached path belongs to the intended
repository and session scope.

## Public GitHub

A package that pushes to `github.com/bitweaver/*` must not mention an
installation's brand, hostnames, tracker ids, or private packages (rule 12).
Rewrite the message before `git push`. Do not push a local public `master`
that is ahead with unrelated session work because one slice lives there;
leave other local commits unpushed unless the user names them.
