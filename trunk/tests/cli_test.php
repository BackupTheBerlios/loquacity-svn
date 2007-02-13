#!/usr/bin/php
<?php
error_reporting(E_ALL);
if( count($argv) > 1){
	require_once('lib/simpletest/web_tester.php');
	require_once('lib/simpletest/reporter.php');
	
	$run = $argv[1];
	$path = 'runs/'.$run.'/Loquacity_config.php';
	#print "$path\n";
	require_once($path);
	if(defined('blog_url')){
		$test =& new GroupTest('LoquacityInstaller');
		$test->addTestFile('webcases/installation.php');
		exit($test->run(new TextReporter()) ? 0 : 1);
	}
	else{
		print "ERROR: Unable to import the test configuration\n";
	}
}
else{
	print "No arguments given.\n";
}
?>