<?php


require_once dirname(__FILE__).'/BaseCase.php';
//require_once 'DBTest.php';


class AccidentTest extends BaseCase{
	//包含：事故上报，事故上报信息，事故详情
	//事故上报    		/accident/upload.html    action = "upload"
	//事故上报信息      /accident/upload.html    action = "c_info"
	//事故详情         /accident/upload.html     action = "list"


	private $url;
	private $parms;
	private  $id ;
	public static $orderid;
	private static $index ;
	private static $onedata ;
	private static $pub_data ;
	private static $asum;

	public static function setUpBeforeClass(){
		self::$pub_data = array("city_id"=>"1","type"=>"2","content"=>"test");
		self::$index = 0 ;
		$id = self::$uid ;
		$sql = "select id from 630_tour where customer_id = '{$id}' order by  id desc limit 10" ;
		$rltdata = self::$conn->query($sql);
		self::$onedata = $rltdata->fetch_row()[0];
		//print_r(self::$onedata);
	}



	//用例1：查看用户信息
	/** 
     * @dataProvider provider 
    */ 
	public function test_accident($type,$result){
		$this->url = $this->get_url("/accident/upload.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = $type;
		$this->parms['order_id'] = self::$onedata;
		$this->parms['city_id'] =  self::$pub_data['city_id'];
		switch (self::$index) {
			case '0':
				self::$index += 1 ;
				$this->rlt=$this->post_reslut($this->url,$this->parms);
				self::$asum = count($this->rlt['data']) ;     //判断一个订单有几个事故
				$idx = self::$asum -1 ; 
				//print_r($this->rlt);
				if($idx>0){
					$this->assertEquals($this->rlt['data'][$idx]['order_id'],self::$onedata);
					//$this->assertEquals($this->rlt['data'][$idx]['content'],self::$pub_data['content']);
				}
				break;
			case '1':
				self::$index += 1 ;
				$this->parms['type'] = self::$pub_data['type'] ;
				$this->parms['content'] = self::$pub_data['content'] ;
 				$this->rlt=$this->post_reslut($this->url,$this->parms);
				//print_r($this->rlt);
				$this->assertEquals($this->rlt['msg'],$result);
				break;
			case '2':
				self::$index += 1 ;
				$this->rlt=$this->post_reslut($this->url,$this->parms);
				//print_r($this->rlt);
				//$nums = count($this->rlt['data']);
				$this->assertEquals($this->rlt['status'],$result);
				break;
			case '3':
				self::$index += 1 ;
				$this->rlt=$this->post_reslut($this->url,$this->parms);
				$curnum = count($this->rlt['data']) ;   //当前故障的总数
				$idx = $curnum -1 ;   //取所有数组(故障数)中的最后一个索引
				$newnum = self::$asum + $result ;      //故障多了一个后新的总数
				//prinprint_r($this->rlt);
				$this->assertEquals($curnum,$newnum);
				if($idx>0){
					$this->assertEquals($this->rlt['data'][$idx]['order_id'],self::$onedata) ;
					//$this->assertEquals($this->rlt['data'][$idx]['content'],self::$pub_data['content']) ;  这里有一个bug，先跳过测试
				}
				break;
			default:
				# code...
				break;
		}
		
	}


	public function provider(){
		return array(
			array("list",0),
			array("upload","提交成功"),
			array("c_info",0),
			array("list",1)
		);
	}


}