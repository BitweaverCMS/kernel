<?php
require_once(KERNEL_PKG_PATH . 'BitPreferences.php');
# require_once(KERNEL_PKG_PATH . 'test/TestBitPreferences.php');

 
class TestBitPreferencesDatabase extends UnitTestCase {
    // class TestBitPreferencesDatabase extends TestBitPreferences {
  
    var $name = "TestBitPreferencesDatabase";
    var $test;

    function TestBitPreferencesDatabase()
    {
	// No general initialization
    }
    
    function setUp ()
    {
	global $gBitDb;
	if (!is_object($gBitDb)) {
	    $this = NULL;
	    return;
	}
	$name = "`" . $this->name . "`";
	if (!$gBitDb->tableExists($name)) {
	    $tables = array($name => "
		  `name` C(50) PRIMARY,
		  `pref_value` C(255)
		");
	}
	global $gCache;
	$tmpCache = $gCache;
	$gCache = NULL;
	$this->test = new BitPreferences($this->name);
	$gCache = $tmpCache;
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