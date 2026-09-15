<?php
/**
 * CLI / cron $_SERVER keys. Load this before setup_inc.php.
 *
 * config/kernel/config_inc.php runs at the top of config_defaults_inc.php and
 * reads SERVER_ADDR / REMOTE_ADDR. Kernel's later $gShellScript defaults are
 * too late to prevent PHP 8 warnings.
 *
 * SCRIPT_URL is the invoked script (CLI SCRIPT_FILENAME), not this include.
 */
$_SERVER['SERVER_NAME'] = gethostname();
$_SERVER['SCRIPT_URL'] = !empty( $_SERVER['SCRIPT_FILENAME'] ) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__;
$_SERVER['REMOTE_ADDR'] = 'cron';
$_SERVER['SERVER_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = 'cron';
$_SERVER['HTTP_HOST'] = '';
$_SERVER['HTTPS'] = 'on';


