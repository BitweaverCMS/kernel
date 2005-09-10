<?php
require_once('bit_setup_inc.php');
require_once(KERNEL_PKG_PATH.'BitPreferences.php');

class TestBitPreferencesCache extends Test {

    var $name = "TestBitPreferencesCache";

    function initBitPreferences()
    {
	global $gBitDb;
	$tmpDB = $gBitDb;
	$gBitDb = NULL;
        $test = new BitPreferences($this->name);
	$gBitDb = $tmpDB;
	return $test;
    }

    function TestBitPreferencesCache()
    {
	global $gCache;
	if (!is_object($gCache)) {
		$this = NULL;
		return;
	}
        $test = $this->initBitPreferences();
        Assert::equals($test != NULL, 'Error during initialisation');
	
    }
    
    function testGetNonexistentItem()
    {
        $test = $this->initBitPreferences();
        Assert::equals($test->getPreference("test"), NULL);
    }
    
    function testSetNonexistentItem()
    {
        $test = $this->initBitPreferences();
        $test->setPreference("test", "123");
        Assert::equals($test->getPreference("test"), "123");
    }
    
    function testSetDefaultItem()
    {
        $test = $this->initBitPreferences();
        $test->setDefaultPreference("test", "456");
        Assert::equals($test->getPreference("test"), "123");
    }
    
    function testSetAsDefaultItem()
    {
        $test = $this->initBitPreferences();
        $test->setDefaultPreference("test", "456");
        $test->setPreference("test", "456");
        Assert::equals($test->getPreference("test"), "456");
    }

    function testResetItem()
    {
        $test = $this->initBitPreferences();
        $test->setDefaultPreference("test", "456");
        $test->setPreference("test", NULL);
        Assert::equals($test->getPreference("test"), "456");
    }
}
?>
