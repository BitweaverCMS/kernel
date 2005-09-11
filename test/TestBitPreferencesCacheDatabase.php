<?php
require_once('bit_setup_inc.php');
require_once(KERNEL_PKG_PATH.'BitPreferences.php');

class TestBitPreferencesCacheDatabase extends Test {

    var $name = "TestBitPreferencesCacheDatabase";

    function initBitPreferences()
    {
        $test = new BitPreferences($this->name, new BitCache(), new BitDb());
	return $test;
    }

    function TestBitPreferencesCacheDatabase()
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
		  `value` C(255)
		");
            $gBitDb->createTables($tables);
        }
        $test = $this->initBitPreferences();
        Assert::equalsTrue($test != NULL, 'Error during initialisation');
	
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
	//$test->mDebug = true;
        $test->setDefaultPreference("test", "456");
        Assert::equals($test->getPreference("test"), "123");
    }

    function testSetAsDefaultItem()
    {
        $test = $this->initBitPreferences();
	//$test->mDebug = true;
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
