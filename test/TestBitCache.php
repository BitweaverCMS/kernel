<?php
require_once('bit_setup_inc.php');

class TestBitCache extends Test {
    
    var $test;
    var $name ="TestBitCache";

    function TestBitCache()
    {
      // global $gBitCache;
      $this->test = new BitCache();
	Assert::equals(is_object($this->test), 'Error during initialisation');
	if (!is_object($this->test)) {
		$this = NULL;	
		return;
	}
	
    }
    
    function testGetNonexistentCachedItem()
    {
      Assert::equals($this->test->getCached("TestBitCache"), NULL);
    }
    
    function testIsNonexistentItemCached()
    {
        Assert::equals($this->test->isCached("TestBitCache"), false);
    }

    function testRemoveNonexistentCachedItem()
    {
        $this->test->removeCached("TestBitCache");
        Assert::equals($this->test->isCached("TestBitCache"), false);
    }
    
    function testSetCachedItem()
    {
        $this->test->setCached("TestBitCache", "123");
        Assert::equals($this->test->isCached("TestBitCache"), true);
    }
    
    function testGetCachedItem()
    {
        Assert::equals($this->test->getCached("TestBitCache"), "123");
    }
    
    function testIsItemCached()
    {
        Assert::equals($this->test->isCached("TestBitCache"), true);
    }

    function testRemoveCachedItem()
    {
        $this->test->removeCached("TestBitCache");
        Assert::equals($this->test->isCached("TestBitCache") ||
        $this->test->getCached("TestBitCache") != NULL, false);
    }
}
?>

