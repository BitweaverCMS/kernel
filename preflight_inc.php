<?php
/**
* * Return system defined temporary directory.
* In Unix, this is usually /tmp
* In Windows, this is usually c:\windows\temp or c:\winnt\temp
* \static
*/
function getTempDir()
{
	static $tempdir;
	if (!$tempdir)
	{
		$tempfile = tempnam(false, 'foo');
		$tempdir = dirname($tempfile);
		@unlink($tempfile);
	}
	return $tempdir;
}

/**
* * Return true if windows, otherwise false
* \static
*/
function isWindows()
{
	static $windows;
	if (!isset($windows))
	{
		$windows = substr(PHP_OS, 0, 3) == 'WIN';
	}
	return $windows;
}

function mkdir_p($target, $perms = 0777)
{
	global $gDebug;
	$target;
	if (file_exists($target) || is_dir($target))
	{
		if ($gDebug) echo "mkdir_p() - file already exists $target<br>";
		return 0;
	}

	if (isWindows()) {
	} else {
		if (substr($target, 0, 1) != '/') {
			if ($gDebug) echo "mkdir_p() - prepending with a /<br>";
			$target = "/$target";
		}
		if( ereg('\.\.', $target) ) {
			if ($gDebug) echo "mkdir_p() - invalid Unix path $target<br>";
			return 0;
		}
	}

	$oldu = umask(0);
	if (@mkdir($target, $perms)) {
		umask($oldu);
		if ($gDebug) echo "mkdir_p() - creating $target<br>";
		return 1;
	} else {
		umask($oldu);
		$parent = substr($target, 0, (strrpos($target, '/')));
		if ($gDebug) {
			echo "mkdir_p() - trying to create parent $parent<br>";
		}
		if (mkdir_p($parent, $perms)) {
			// make the actual target!
			@mkdir($target, $perms);
			return 1;
		}
	}
}

/*
function mkdir_p($target, $perms = 0777)
{
	global $gDebug;

	if (file_exists($target) || is_dir($target))
	{
		if ($gDebug) echo "mkdir_p() - file already exists $target<br>";
		return 0;
	}

	if (isWindows()) {
	} else {
		if (substr($target, 0, 1) != '/') {
			if ($gDebug) echo "mkdir_p() - prepending with a /<br>";
			$target = "/$target";
		}
		if (ereg('\.\.', $target) || !preg_match("/[-a-zA-Z0-9\._\/]*-space-/", $target)) {
			if ($gDebug) echo "mkdir_p() - invalid Unix path $target<br>";
			return 0;
		}
	}

	$oldu = umask(0);
	if (@mkdir($target, $perms)) {
		umask($oldu);
		if ($gDebug) echo "mkdir_p() - creating $target<br>";
		return 1;
	} else {
		umask($oldu);
		$parent = substr($target, 0, (strrpos($target, '/')));
		if ($gDebug) {
			echo "mkdir_p() - trying to create parent $parent<br>";
		}
		if (mkdir_p($parent, $perms)) {
			mkdir_p($target, $perms);
		}
	}
}
*/

/**
* Check minimum PHP version
* @param pVersion is the minimum PHP version required
**/
function chkPhpVersion($pVersion)
{
	$this->chkPhpExtension("", $pVersion);
}

/**
* Check minimum PHP extension version
* @param pExtension is the extension name
* @param pVersion is the minimum extension version required
**/
function chkPhpExtension($pExtension, $pVersion)
{
	$success = version_compare(phpversion($pExtension), $pVersion, "<");
	$pExtension = ($pExtension != "") ? $pExtension : "PHP";
	return $success;
}

/**
* Used to check if files are writeable by the webserver
* @param pFile the name of the file to be checked
**/
function isFileWriteable($pFile)
{
	$success = 1;
	if (!@file_exists($pFile)) {
		$success = 0;
		$data = "not exist";
	} elseif (!@is_file($pFile)) {
		$success = 0;
		$data = "not file";
	} elseif (!@is_writable($pFile)) {
		$success = 0;
		$data = "not writeable";
	}
	return $success;
	// $data is currently redundant
}

/**
* Used to check if directories are writeable by the webserver
* @param pDir the name of the directory to be checked
**/
function isDirectoryWriteable($pDir)
{
	$success = 1;
	if (!@file_exists($pDir)) {
		$success = 0;
		$data = "not exist";
	} elseif (!@is_dir($pDir)) {
		$success = 0;
		$data = "not directory";
	} elseif (!@is_writable($pDir)) {
		$success = 0;
		$data = "not writeable";
	}
	return $success;
	// $data is currently redundant
}

/**
* Used to check php.ini settings
* @param pName setting name
* @param pValue setting value
* @param pComp setting comparison
**/
function chkPhpSetting($pName, $pValue, $pComp='')
{
	$actual = ini_get($pName);
	eregi("^([0-9]+)[KMG]$", $actual, $x);
	$actual = (isset($x)) ? $x[1] : $actual;
	switch($pComp) {
		case ">=":
			$success = ($actual >= $pValue) ? 1 : 0;
			break;
		default:
			$success = ($actual == $pValue) ? 1 : 0;
	}
	return $success;
	// redundant $data = serialize(array("check" => $pValue, "actual" => $actual));
}
?>
