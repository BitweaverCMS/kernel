<?php
require_once(KERNEL_PKG_PATH.'BitPreferences.php');
#require_once(KERNEL_PKG_PATH.'test/TestBitPreferences.php');

class TestBitPreferencesCacheDatabase extends UnitTestCase {
    //class TestBitPreferencesCacheDatabase extends TestBitPreferences {

    var $name = "TestBitPreferencesCacheDatabase";
    var $test;
  
    function TestBitPreferencesCacheDatabase()
    {
    }
  
    function setUp ()
    {
	global $gBitDb, $gBitCache;
	$gBitCache = new BitCache();
	if (!is_object($gBitDb) || !is_object($gBitCache)) {
	    $this = NULL;
	    return;
	}
	$name = "`".$this->name."`";
	if (!$gBitDb->tableExists($name)) {
	    $tables = array(
			    $name => "
		  `name` C(50) PRIMARY,
		  `pref_value` C(255)
		");
	    $gBitDb->createTables($tables);
	}
	$this->test = new BitPreferences($this->name);
	
	// This test can not be performed in the constructor in simpleTest
	$this->assertTrue($this->test != NULL, 'Error during initialisation');
    }


    function tearDown ()
    {
	global $gBitDb;
	$name = "`".$this->name."`";
	$tables = array ($name);
	$gBitDb->dropTables($tables);
	$this->test = NULL;
    }


    // Tests duplicated from TestBitPreferences
    // Could not inherit them like in TestBitPreferencesCache for some reason.
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