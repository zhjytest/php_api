<?php


require_once 'BaseCase.php';
// require_once 'CommonClass/ApiName.php';
// require_once 'CommonClass/Const.php';

class TestTest extends BaseCase{


	public $result;
	private $parms;
	private static $pub_data;
	// public static function setUpBeforeClass(){
		
	// }



	public function test_a(){
		//$curtime = $this->getvalue('yz','curtime') ;
		$urls = $this->getvalue('yz','apiurl') ;
		//$urls1 = "http://123.57.217.108:6600/test.php?uptime=".$curtime ;
		$this->result = $this->get_reslut($urls);
		print_r($this->result) ;
		//$curtime = $this->result['cur_time'] ;
		//print_r($curtime) ;
	}


}