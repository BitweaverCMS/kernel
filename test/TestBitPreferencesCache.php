<?php
require_once(KERNEL_PKG_PATH.'BitPreferences.php');
require_once(KERNEL_PKG_PATH.'test/TestBitPreferences.php');
# class TestBitPreferencesCache extends UnitTestCase {

class TestBitPreferencesCache extends TestBitPreferences {


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
	// $this->assertNotNull($this->test),
	//                    'Error during initialisation');
	// This check is probably not needed - it only adds to the passes
	// and if it is not true it does not save anything anyway.
    }

    function tearDown ()
    {
	$this->test = NULL;
    }


  /*

  We are using inheritance instead
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
	#global $gCache;
	#if (!is_object($gCache)) {
	#  print "We have LANDED <br />\n";
	#	$this = NULL;
	#	return;
	#}
        $test = $this->initBitPreferences();
	// This check is probably not needed - it only adds to the passes
	// and if it is not true it does not save anything anyway.

        // Assert::equals($test != NULL, 'Error during initialisation');
	
    }
    
    function testGetNonexistentItem()
    {
        $test = $this->initBitPreferences();
        $this->assertNull($test->getPreference("test"));
    }
    
    function testSetNonexistentItem()
    {
        $test = $this->initBitPreferences();
        $test->setPreference("test", "123");
        $this->assertEqual($test->getPreference("test"), "123");
    }
    
    function testSetDefaultItem()
    {
        $test = $this->initBitPreferences();
        $test->setDefaultPreference("test", "456");
        $this->assertEqual($test->getPreference("test"), "123");
    }
    
    function testSetAsDefaultItem()
    {
        $test = $this->initBitPreferences();
        $test->setDefaultPreference("test", "456");
        $test->setPreference("test", "456");
        $this->assertEqual($test->getPreference("test"), "456");
    }

    function testResetItem()
    {
        $test = $this->initBitPreferences();
        $test->setDefaultPreference("test", "456");
        $test->setPreference("test", NULL);
        $this->assertEqual($test->getPreference("test"), "456");
    }
  */

}
?>
