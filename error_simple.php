<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/error_simple.php,v 1.2 2005/06/28 07:45:45 spiderr Exp $
 * @package kernel
 * @subpackage functions
 */

echo '<html><body><pre><p>';
if (isset($_REQUEST['error']) and !is_null($_REQUEST['error'])) {
	echo strip_tags($_REQUEST['error']);
} else {
	echo 'There was an unsepecified error.  Please go back and try again.';
}
?>
</p>
<form name="loginbox" action="../users/validate.php" method="post">
user: <input type="text" name="user"  size="20" /><br />
pass: <input type="password" name="pass" size="20" /><br />
<input type="submit" name="login" value="login" /></form>

<p><a href="javascript:history.back()">Go back</a></p></pre></body></html>

