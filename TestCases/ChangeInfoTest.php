<?php


require_once dirname(__FILE__).'/BaseCase.php';
require_once dirname(__FILE__).'/../CommonClass/pubfunction.php';



class ChangeInfoTest extends BaseCase{
	//包含：上传用户头像,修改个人信息，用户信息
	//上传用户头像    		/upload/head-image.html  
	//备注：其中用户信息已经单独写过，这里的调用主要是为了上传头像后查看其地址。 



	private $url;
	private $parms;
	private $obj ;
	private static $pub_data ;
	private static $filename ;
	private static $name ;
	private static $sex ;


	public static function setUpBeforeClass(){
		self::$pub_data = array("type"=>"user");
		self::$name = generate_password() ;
		self::$sex = rand(1,3);
	}


	//用例1：查看图片url
	public function test_headurl1(){
		$this->url = $this->get_url("/customer/info.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['type'] = self::$pub_data['type'];
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt['data']['head_image_url']);
		$imgurl = $this->rlt['data']['head_image_url'] ;
		$arr = explode('/',$imgurl) ;
		self::$filename = end($arr);
		$this->assertContains('head_image',$this->rlt['data']['head_image_url']);
		$this->assertContains('jpg',$this->rlt['data']['head_image_url']);
	}

	//用例2：进行图片上传修改
	/** 
     * @dataProvider provider 
    */ 
	public function test_headimage($type,$result){
		$file = filepwd($type) ;
		//echo $file ;
		$this->obj = new CURLFile($file);
		$this->url = $this->get_url("/upload/head-image.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['file'] = $this->obj;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['data'],$result);
				
	}


	//用例3：修改个人信息
	public function test_changeprofile(){
		$this->url = $this->get_url("/customer/change-profile.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['name'] = self::$name;
		$this->parms['sex'] = self::$sex;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['status'],0);
				
	}


	//用例3：再次查看图片url及修改后的信息
	public function test_headurl2(){
		$this->url = $this->get_url("/customer/info.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['type'] = self::$pub_data['type'];
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		$curimgurl = $this->rlt['data']['head_image_url'] ;
		$arr1 = explode('/',$curimgurl) ;
		$curfilename = end($arr1) ;
		//print_r($curfilename);
		$this->assertNotEquals($curfilename,self::$filename);
		$this->assertEquals($this->rlt['data']['name'],self::$name);    //查看用户的姓名是否修改
		$this->assertEquals($this->rlt['data']['gender'],self::$sex);	//查看用户的性别是否修改
	}



	public function provider(){
		return array(
			array("test.jpg","上传头像成功")
		);
	}


}