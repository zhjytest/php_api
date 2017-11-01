<?php


require_once dirname(__FILE__).'/BaseCase.php';
//require_once 'DBTest.php';


class CustomerInfoTest extends BaseCase{
	//包含：用户信息，未读活动数量，未读消息数量，消息列表消息 ，以上都是同一个接口，不同的参数
	//用户信息       /customer/info.html    type = "user"
	//未读活动数量    /customer/info.html    type = "act_unread"
	//未读消息数量    /customer/info.html    type = "msg_unread"
	//消息列表消息    /customer/info.html    type = "msg"


	private $url;
	private $parms;
	public static $carid;
	public static $pricekm;
	public static $pricemin;
	public static $orderid;
	private static $index ;
	
	//private $stack ;
	public static function setUpBeforeClass(){
		self::$index = 0 ;
	}



	//用例1：查看用户信息
	/** 
     * @dataProvider provider 
    */ 
	public function test_customerinfo($type,$result){
		$this->url = $this->get_url("/customer/info.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		//$this->parms['type'] = self::$pub_data['type_info'];
		$this->parms['type'] = $type;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['status'],0);
		if($this->rlt['status']==0){
			switch (self::$index) {
				case '0':
					self::$index += 1 ;
					//$rslt =  
					$this->assertEquals($this->rlt['data']['mobile'],self::$mobile);
					break;
				case '1':
					self::$index += 1 ;
					$nums = count($this->rlt['data']);
					$this->assertLessThanOrEqual($nums,$result);
					break;
				case '2':
					self::$index += 1 ;
					$this->assertLessThanOrEqual($this->rlt['data']['msg_unread'],$result);
					break;
				case '3':
					self::$index += 1 ;
					$this->assertLessThanOrEqual($this->rlt['data']['act_unread'],$result);
					break;
				case '4':
					self::$index += 1 ;
					//$content = $this->rlt['data'][0]
					$nums = count($this->rlt['data'][0]);
					//print_r("$content");
					if($nums != 0){
						$this->assertLessThan($nums,$result);
					}
				default:
					# code...
					break;
			}
		}else{
			self::$index += 1;
			echo "返回状态码：".$this->rlt['status'];
		}
		
	}


	public function provider(){
		//$m = self::$mobile ;
		return array(
			array("user",0),
			array("msg",0),
			array("msg_unread",0),
			array("act_unread",0),
			array("coupon",0)
		);
	}


}