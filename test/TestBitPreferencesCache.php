<?php
require_once(KERNEL_PKG_CLASS_PATH.'BitPreferences.php');
require_once(KERNEL_PKG_PATH.'test/TestBitPreferences.php');
# class TestBitPreferencesCache extends UnitTestCase {

class TestBitPreferencesCache extends TestBitPreferences {


    var $name = "TestBitPreferencesCache";

    function setUp ()
    {
	global $gBitDb, $gCache;
	$tmpDB = $gBitDb;
	$tmpCache = $gCache;
	$gBitDb = NULL;
	$gCache = NULL;
	$this->test = new BitPreferences("TestBitPreferencesCache");
	$this->test->mDebug = false;
	$gBitDb = $tmpDB;
	$gCache = $tmpCache;
    }

    function tearDown ()
    {
	$this->test = NULL;
    }


# [tests]

    function TestBitPreferencesCache()
    {
      // no general initalization
	
    }
    
    function testGetNonexistentItem()
    {
        $this->assertNull($this->test->getPreference("test"));
    }
    
    function testSetNonexistentItem()
    {
        $this->test->setPreference("test", "123");
        $this->assertEqual($this->test->getPreference("test"), "123");
    }
    
    function testSetDefaultItem()
    {
	$this->test->setPreference("test", "123");
        $this->test->setDefaultPreference("test", "456");
        $this->assertEqual($this->test->getPreference("test"), "123");
    }
    
    function testSetAsDefaultItem()
    {
        $this->test->setDefaultPreference("test", "456");
        $this->test->setPreference("test", "456");
        $this->assertEqual($this->test->getPreference("test"), "456");
    }

    function testResetItem()
    {
        $this->test->setDefaultPreference("test", "456");
        $this->test->setPreference("test", NULL);
        $this->assertEqual($this->test->getPreference("test"), "456");
    }
}
?>
