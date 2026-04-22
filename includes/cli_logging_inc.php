<?php

require_once( 'classes/BitCli.php' );

$cli = new BitCliArgs(__FILE__);
$cli->addOption('site_name', 'Printmotive Site', NULL, true);
$cli->addOption('delay', 'Delay script start', 0, false);
$cli->addOption('max_queue', 'Max entries allowed in 1Q and 2P status (ie in the print queue)', 10);
$cli->addOption('log_file', 'Path to log file', '/var/log/httpd/jobs/'.$fileRootName.'_log', true);
$cmdOptions = $cli->parse();
putenv( 'SITE_NAME='.$cmdOptions['site_name'] );

if( !empty( $cmdOptions['log_file'] ) ) {
	require_once( 'classes/BitLogger.php' );
	$logger = new BitLogger( $cmdOptions['log_file'], $_SERVER['SITE'] );
	$logger->info( "Checking for orders" );
}

if( !empty( $cmdOptions['debug'] ) ) {
	$gDebug = TRUE;
}

