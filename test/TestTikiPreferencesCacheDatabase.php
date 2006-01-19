<?php
require_once('bit_setup_inc.php');
require_once(KERNEL_PKG_PATH.'BitPreferences.php');

class TestBitPreferencesCacheDatabase extends Test {

    var $name = "TestBitPreferencesCacheDatabase";

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
        $test = new BitPreferences($this->name);
        Assert::equalsTrue($test != NULL, 'Error during initialisation');
	
    }
    
    function testGetNonexistentItem()
    {
        $test = new BitPreferences($this->name);
        Assert::equals($test->getPreference("test"), NULL);
    }
    
    function testSetNonexistentItem()
    {
        $test = new BitPreferences($this->name);
        $test->setPreference("test", "123");
        Assert::equals($test->getPreference("test"), "123");
    }

    function testSetDefaultItem()
    {
	$test = new BitPreferences($this->name);
	//$test->mDebug = true;
        $test->setDefaultPreference("test", "456");
        Assert::equals($test->getPreference("test"), "123");
    }

    function testSetAsDefaultItem()
    {
        $test = new BitPreferences($this->name);
	//$test->mDebug = true;
        $test->setDefaultPreference("test", "456");
        $test->setPreference("test", "456");
        Assert::equals($test->getPreference("test"), "456");
    }

    function testResetItem()
    {
        $test = new BitPreferences($this->name);
        $test->setDefaultPreference("test", "456");
        $test->setPreference("test", NULL);
        Assert::equals($test->getPreference("test"), "456");
    }
}
?>
