<?php

	require_once( '../../kernel/includes/setup_inc.php' );

	if (!$gBitUser->isAdmin()) {
		$gBitSmarty->assign('msg', tra("You dont have permission to use this feature"));
		$gBitSystem->display( 'error.tpl' , NULL, array( 'display_mode' => 'admin' ));
		die;
	}

	if( defined( 'DB_PERFORMANCE_STATS' ) && constant( 'DB_PERFORMANCE_STATS' ) ) {
?>

<h1 style="color:red;">Performance Monitoring is Active!</h1>
<p>Database performance monitoring is a low level, and intensive task. <b>It should not be left on for extended periods of time.</b>

<?php
	} else {
?>

<p style="color:red;">To activate database performance, please add to you config/kernel/config_inc.php:<br>
<code>define( 'DB_PERFORMANCE_STATS', TRUE ); </code>

<?php
	}
?>
For more information, see the <a href="http://phplens.com/lens/adodb/docs-perf.htm">ADODB documentation</a>
</p>

<?php
	$perf =& NewPerfMonitor( $gBitSystem->mDb->mDb );
	$perf->UI($pollsecs=5);
?>
