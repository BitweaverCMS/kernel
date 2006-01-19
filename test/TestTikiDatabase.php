<?php
require_once('bit_setup_inc.php');

class TestTikiDatabase extends Test {

    var $test;
    var $name = "TestTikiDatabase";
    var $data = "GIF89a  ¢     ÿÿÿÿ   ÿ  ÿÿÿ      !ù   ,       -D£ÑkÈI¡4¼©;éZE-dUèÍ&¹ËåRlvFï¼ÞÓpþ ;";


    function TestTikiDatabase()
    {
        global $gBitDb;
        $this->test = $gBitDb;
        Assert::equals($this->test, !NULL);
	if (!is_object($this->test)) {
		$this = NULL;
		return;
	}
    }

    function testTableExists()
    {
	$table = "`".$this->name."`";
        Assert::equals($this->test->tableExists($table), false, 'Error test table already exists');
    }

    function testCreateTable()
    {
	$name = "`".$this->name."`";
        if($this->test->tableExists($name)) {
            Assert::assert('Skipped - test table already exists');
	    return;
        }
		
	$tables = array(
		$name => "
		  `uniqueId` I4 AUTO PRIMARY,
		  `someText` C(15),
		  `someDate` I8,
		  `someBlob` B
		");
        Assert::equals($this->test->createTables($tables), 'Error creating test table');
    }

/*    function testQStr()
    {
        $x = " \' \" 123 \" \' ";
        Assert::equals($this->test->qstr($x) == " \' \" 123 \" \' ");
    }
*/
    function testInsertData()
    {
	$now = date("U");
	$query = "INSERT INTO `".$this->name."` (`someText`, `someDate`) VALUES (?,?)";
	$bindvars = array("abc", (int)$now);
        $result = $this->test->query($query, $bindvars);
        Assert::equals(is_object($result), true);
	$bindvars = array("xyz", 1234);
	$this->test->query($query, $bindvars);
	$bindvars = array("ABC", 6789);
	$this->test->query($query, $bindvars);
    }

    function testSelectData()
    {
	$query = "SELECT * FROM `".$this->name."`";
        $result = $this->test->query($query);
        Assert::equals($result->numRows(), 3);
    }

    function testGetOneField()
    {
	$query = "SELECT `someText` FROM `".$this->name."` WHERE `someDate` = ?";
	$bindvars = array(6789);
        $result = $this->test->getOne($query, $bindvars);
        Assert::equals($result, "ABC");
    }

    function testDeleteData()
    {
	$query = "DELETE FROM `".$this->name."`";
        $result = $this->test->query($query);
        Assert::equals($result, true);
    }

    function testEncodeBlob()
    {
	$data = $this->data;
	$now = '1234';
	$query = "INSERT INTO `".$this->name."` (`someBlob`, `someDate`) VALUES (?,?)";
	$bindvars = array($this->test->db_byte_encode($data), (int)$now);
        $result = $this->test->query($query, $bindvars);
        Assert::equals(is_object($result), true);
    }

    function testDecodeBlob()
    {
	$data = $this->data;
        $now = '1234';
	$query = "SELECT `someBlob` FROM `".$this->name."` WHERE `someDate` = ?";
        $bindvars = array($now);
        $result = $this->test->getOne($query, $bindvars);
        Assert::equals($this->test->db_byte_decode($result), $data);
    }

    function testDropTable()
    {
	$tables = array("`".$this->name."`");
        Assert::equals($this->test->dropTables($tables), true);
    }
}
?>

