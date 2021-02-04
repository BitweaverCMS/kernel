<?php
require_once(KERNEL_PKG_CLASS_PATH.'BitPreferences.php');

class TestBitPreferences extends UnitTestCase {

    var $test;
    
    function TestBitPreferences()
    {
	// no general initialization
    }
    
    function setUp ()
    {
	global $gBitDb, $gCache;
	$tmpDB = $gBitDb;
	$tmpCache = $gCache;
	$gBitDb = NULL;
	$gCache = NULL;
	$this->test = new BitPreferences("TestBitPreferences");
	$this->test->mDebug = false;
	$gBitDb = $tmpDB;
	$gCache = $tmpCache;
	// $this->assertNotNull($this->test),
	//                    'Error during initialisation');
	// This check is probably not needed - it only adds to the passes
	// and if it is not true it does not save anything anyway.
    }


    function tearDown ()
    {
	$this->test = NULL;
    }

    function testGetNonexistentItem()
    {
      $this->assertNull($this->test->getPreference("test"));
    }
    
    function testSetNonexistentItem()
    {
        $this->test->setPreference("test", "123");
        $this->assertEqual($this->test->getPreference("test"), "123", "");
    }
    
    function testSetDefaultItem()
    {
	$this->test->setPreference("test", "123");
        $this->test->setDefaultPreference("test", "456");
        $this->assertEqual($this->test->getPreference("test"), "123");
    }

    function testSetAsDefaultItem()
    {
	$this->test->setPreference("test", "123");
        $this->test->setDefaultPreference("test", "456");
        $this->test->setPreference("test", "456");
        $this->assertEqual($this->test->getPreference("test"), "456");
    }

    function testReadDefaultItem()
    {
	$this->test->setPreference("test", "123");
        $this->test->setDefaultPreference("test", "456");
        $this->test->setPreference("test", NULL);
        $this->assertEqual($this->test->getPreference("test"), "456");
    }

    function testResetItem()
    {
	$this->test->setPreference("test", "123");
        $this->test->setPreference("test", NULL);
        $this->assertNull($this->test->getPreference("test"));
    }
}
?>
