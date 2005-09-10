<?php
require_once('bit_setup_inc.php');
require_once(KERNEL_PKG_PATH.'BitPreferences.php');

class TestBitPreferences extends Test {

    var $test;
    
    function TestBitPreferences()
    {
	global $gBitDb, $gCache;
	$tmpDB = $gBitDb;
	$tmpCache = $gCache;
	$gBitDb = NULL;
	$gCache = NULL;
        $this->test = new BitPreferences("TestBitPreferences");
	$gBitDb = $tmpDB;
	$gCache = $tmpCache;
        Assert::equalsTrue($this->test != NULL, 'Error during initialisation');
    }
    
    function testGetNonexistentItem()
    {
        Assert::equals($this->test->getPreference("test"), NULL);
    }
    
    function testSetNonexistentItem()
    {
        $this->test->setPreference("test", "123");
        Assert::equals($this->test->getPreference("test"), "123");
    }
    
    function testSetDefaultItem()
    {
        $this->test->setDefaultPreference("test", "456");
        Assert::equals($this->test->getPreference("test"), "123");
    }

    function testSetAsDefaultItem()
    {
        $this->test->setPreference("test", "456");
        Assert::equals($this->test->getPreference("test"), "456");
    }

    function testResetItem()
    {
        $this->test->setPreference("test", NULL);
        Assert::equals($this->test->getPreference("test"), "456");
    }
}
?>
