<?php


require_once dirname(__FILE__).'/BaseCase.php';

class AAArandstrTest extends BaseCase{

	private $url;
	private $parms ;
	private $rlt ;
	private $rdstr;

	//@beforeClass;
	function setUp(){
		$this->url = $this->get_url('/login/code.html');
		$this->parms  = $this->get_parms();
		//$this->parms['phone'] = '15810553242';
		$this->parms['phone'] = $this->getvalue('login','phone');
		//$this->cp = new CurlOperation();
		$this->rlt = $this->post_reslut($this->url,$this->parms);
		$this->rdstr = $this->rlt['data']['randstr'];
		//echo "====set randstr========";
		//print_r($this->rdstr);
		//setrandstr($this->rdstr);
	}

	function test_str_isnotnull(){
		$this->assertNotEmpty($this->rdstr);
	}

}


