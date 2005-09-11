<?php

require_once('bit_setup_inc.php');
// require_once(KERNEL_PKG_PATH.'BitPreferences.php');


class InputOutputTester {

}

class TestBitSmartyFilter extends Test {

  var $filterTestDir;
  var $smartyDir;
  

  function TestBitSmartyFilter ()
  {
    // directory that contains test directories
    $this->filterTestDir = 'smarty_filter_tests';
    $this->smartyDir = KERNEL_PKG_PATH . 'smarty_bit';
  }

  function testPrePostFilters ()
  {
    global $gBitLanguage;
    $gBitLanguage->mLanguage = 'sv';

    Assert::assert(is_dir("$this->filterTestDir"), // Quite fatal
		   "$this->filterTestDir is not a directory");

    // echo "$this->filterTestDir<br />";
    
    $filterTestDirHandle = opendir($this->filterTestDir);
    while (false !== ($filterTestCase = readdir($filterTestDirHandle))) {
      $filterTestCaseDir = $this->filterTestDir . '/' . $filterTestCase;
      Assert::assert(is_dir ($filterTestCaseDir),
		     "$filterTestCaseDir is not a directory");
      
      // echo "$filterTestCaseDir<br />";
      if (preg_match('/(.+)filter\.(.+)/', $filterTestCase, $matches)) {
	// echo "$matches[0]<br />";
	
	$filterType = $matches[1];
	$filterBase = $matches[2];

	$smartyFile = 
	  $this->smartyDir . '/' . $filterType .'filter.' . $filterBase . '.php';
	$filterName = 'smarty_' . $filterType . 'filter_' .$filterBase;

	// echo "$smartyFile<br />";
	// echo "$filterName<br />";

	Assert::equalsTrue(file_exists ($smartyFile),
			   "Smarty filter $smartyFile is missing");
	include_once($smartyFile);

	// echo "$filterTestCaseDir<br />";
	$filterTestCaseDirHandle = opendir("$filterTestCaseDir");
	while (false != ($inputFile = readdir($filterTestCaseDirHandle))) {
	  if (preg_match('/^(.+)\.input$/', $inputFile, $matches)) {
	    $baseName = $matches[1];
	    $outputFile = $filterTestCaseDir . '/' . $baseName . '.output';
	    $errorFile  = $filterTestCaseDir . '/' . $baseName . '.error';
	    $inputFile  = $filterTestCaseDir . '/' . $inputFile;
	    

	    // echo "$inputFile<br />";
	    // echo "$outputFile<br />";
	    // echo "$errorFile<br />";
	
	    Assert::equalsTrue(file_exists ($inputFile),
			       "Input file <strong>$inputFile</strong> " .
			       "is missing");

	    // remove error file if there is no error

	    if (file_exists($errorFile)) {
	      @unlink ($errorFile); 
	    }
	    
	    $input = file_get_contents($inputFile);
	    // $filterOutput = call_user_func ($filterName, $input, &$smarty);
	    $filterOutput = call_user_func_array ($filterName, 
						  array($input, &$gBitSmarty));
	    
	    if (!file_exists($outputFile)) {
	      // Output file does not exist - Create error file
	      // echo "OUTPUT does not Exists<br />";
	      Assert::equalsTrue(file_exists ($outputFile),
				 "Output file <strong>$outputFile</strong> " . 
				 "is missing, " .
				 "<strong>$errorFile</strong> created.");

	      // Error handling missing when writing to file,
	      // final code should be encapsulated in a function.
	      $outHandle = fopen ($errorFile, 'wb');
	      fwrite ($outHandle, $filterOutput, strlen ($filterOutput));
	      fclose($outHandle);
	      // echo "END OUTPUT does not Exists<br />";
	      // break;
	    }
	    
	    else {
	      // echo "OUTPUT Exists<br />";
	      $output = file_get_contents($outputFile);
	      $compareResult = strcmp ($output, $filterOutput);
	      Assert::equals(0, $compareResult, 
			     "<strong>$inputFile</strong> did not match " .
			     "output, incorrect data stored in " .
			     "<strong>$errorFile</strong>");
	      if (0 != $compareResult) {
		$outHandle = fopen ($errorFile, 'wb');
		fwrite ($outHandle, $filterOutput, strlen ($filterOutput));
		fclose($outHandle);
	      }
	      // echo "END OUTPUT Exists<br />";
	    }
	  }
	}
      }
    }
  }
}

?>
