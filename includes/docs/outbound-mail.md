# Outbound mail

Open this file when changing how email is sent or gated.

## Gate

`bit_outbound_mail_allowed()` in `includes/bit_error_inc.php` is the allow check. Live sites (`BitSystem::isLive()`, else the `IS_LIVE` constant) always send. The installer (`BIT_INSTALL`) always sends. Non-live sites send only when the current user has `p_admin`. A missing `$gBitUser` on a non-live site is deny.

`bit_mail()` is the drop-in for PHP `mail()`. It logs a one-line skip (`to` and `subject`, no body) and returns `FALSE` when not allowed.

## Hubs

PHP `mail()` cannot be overridden. SMTP paths skip it. Call the allow check from every send hub:

- `bit_mail()` / `bit_error_email()`
- `BitSystem::sendEmail()`
- `BitMailer::sendEmail()` (PHPMailer may use SMTP)
- Commerce `zen_mail()` / `email::send()` (may use `smtpmail()`)

First-party code should call `bit_mail()` instead of `mail()`. Do not edit vendored PHPMailer or ADOdb.
