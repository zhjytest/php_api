<?php

require_once dirname(__FILE__).'/BaseCase.php';

class AALoginTest extends BaseCase{


	private $url;
	private $parms;
	private $rlt;


	/**
	 * 登录
	 */
	public function setUp(){
		$this->url = $this->get_url("/login/verify.html");
		$this->parms  = $this->get_parms();
		$this->parms['phone'] = $this->getvalue('login','phone');
		$this->parms['code'] = $this->getvalue('login','code');
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt['status']);
		self::$key = $this->rlt['data']['check_key'];
		self::$uid = $this->rlt['data']['id'];
		self::$mobile = $this->rlt['data']['mobile'];
		self::$code = $this->rlt['data']['code'];
		//print_r(self::$key);
		self::$conn = $this->getConnection();
	}

	//用例1：登录成功
	public function test_login_success(){
		//print_r($this->rlt['status']);
		$this->assertEquals($this->rlt['status'],0);
	}


}