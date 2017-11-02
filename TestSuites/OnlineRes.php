<?php


//require_once 'TestCases/BaseCase.php';

class OnlineRes extends PHPUnit_Framework_TestSuite{
	public function __construct(){
		$this->addTestFile('TestCases/AAArandstrTest.php');
		$this->addTestFile('TestCases/AALoginTest.php');
		$this->addTestFile('TestCases/YzBCarTest.php');
		$this->addTestFile('TestCases/CarTest.php');
		$this->addTestFile('TestCases/YzActivityCarTest.php');
		$this->addTestFile('TestCases/YzACarTest.php');
		$this->addTestFile('TestCases/CustomerInfoTest.php');
		$this->addTestFile('TestCases/InvoiceTest.php');
		$this->addTestFile('TestCases/ChangeInfoTest.php');
		$this->addTestFile('TestCases/AccidentTest.php');
		$this->addTestFile('TestCases/ActivitycurTest.php');
		$this->addTestFile('TestCases/ActivityListTest.php');
		$this->addTestFile('TestCases/ShareContentsTest.php');
		$this->addTestFile('TestCases/CodeuseTest.php');
		$this->addTestFile('TestCases/BreakDownTest.php');
		$this->addTestFile('TestCases/FeedBackTest.php');
		$this->addTestFile('TestCases/ActivitycurTest.php');
	}

	public static function suite(){
		return new self();
	}
}