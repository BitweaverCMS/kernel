<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/error_simple.php,v 1.4 2008/08/04 18:14:52 laetzer Exp $
 * @package kernel
 * @subpackage functions
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="icon" href="/favicon.ico" type="image/x-icon" />
		<style type="text/css">
			body {margin:4em;font-family:monospace;line-height:2em;background:#fff}
			fieldset {border:0px solid #fff;margin:0;padding:0}
			legend {display:none;margin:0;padding:0;line-height:0}
			label {display:block;clear:both}
			input[type='submit'] {margin-left:4em;font-family:monospace}
			hr {border:1px solid #ddd;border-width:1px 0 0 0;background:#fff;height:1px;line-height:1px;margin:1em 0}
		</style>
	</head>
	<body>

		<?php
			if (isset($_REQUEST['error']) and !is_null($_REQUEST['error'])) {
				echo strip_tags($_REQUEST['error']);
			} else {
				echo 'There was an unspecified error. Please go back and try again.';
			}
		?>
		
		<hr />
			
		<form name="loginbox" action="<?php echo USERS_PKG_URL ?>validate.php" method="post">
			<fieldset>
				<legend>Login</legend>
				<label for="user">User: <input id="user" name="user" size="20" type="text" /></label>
				<label for="pass">Pass: <input id="pass" name="pass" size="20" type="password" /></label>
				<input type="submit" name="login" value="Login" />
			</fieldset>
		</form>

	</body>
</html>