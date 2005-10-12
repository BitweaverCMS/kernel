<?php
require_once('bit_setup_inc.php');

class TestBitCache extends UnitTestCase {
    
    var $test;
    var $name ="TestBitCache";

    function TestBitCache()
    {
      // global $gBitCache;
      $this->test = new BitCache();
      ## This test can not be performed in the constructor in simpleTest
      # $this->assertTrue(is_object($this->test), 'Error during initialisation');
      if (!is_object($this->test)) {
	$this = NULL;	
	return;
      }
	
    }
    
    function testGetNonexistentCachedItem()
    {
      $this->assertNull($this->test->getCached("TestBitCache"));
    }
    
    function testIsNonexistentItemCached()
    {
        $this->assertFalse($this->test->isCached("TestBitCache"));
    }

    function testRemoveNonexistentCachedItem()
    {
        $this->test->removeCached("TestBitCache");
        $this->assertFalse($this->test->isCached("TestBitCache"));
    }
    
    function testSetCachedItem()
    {
        $this->test->setCached("TestBitCache", "123");
        $this->assertTrue($this->test->isCached("TestBitCache"));
    }
    
    function testGetCachedItem()
    {
        $this->assertEqual($this->test->getCached("TestBitCache"), "123");
    }
    
    function testIsItemCached()
    {
        $this->assertTrue($this->test->isCached("TestBitCache"));
    }

    function testRemoveCachedItem()
    {
        $this->test->removeCached("TestBitCache");
        $this->assertFalse($this->test->isCached("TestBitCache") ||
			   $this->test->getCached("TestBitCache") != NULL);
    }
}
?>