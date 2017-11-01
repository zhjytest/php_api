<?php


require_once dirname(__FILE__).'/BaseCase.php';
require_once dirname(__FILE__).'/../CommonClass/ApiParms.php';


class BreakDownTest extends BaseCase{
	//包含：故障详情，故障上传
	//故障上报    		/break-down/upload.html    action = "list"
	//事故详情         /break-down/upload.html     action = "upload"


	private $url;
	private $parms;
	private  $id ;
	public static $orderid;
	private static $index ;
	private static $onedata ;
	private static $pub_data ;
	private static $asum;
	private static $bu ;

	public static function setUpBeforeClass(){
		self::$pub_data = array("city_id"=>"1","city"=>"北京市","content"=>"test","action_detail"=>"tour_detail");
		self::$index = 0 ;
		self::$bu = rand(1,8) ;
		$id = self::$uid ;
		$sql = "select id from 630_tour where customer_id = '{$id}' order by  id desc limit 10" ;
		$rltdata = self::$conn->query($sql);
		self::$onedata = $rltdata->fetch_row()[0];
		//print_r(self::$onedata);
	}



	//第一次查看行程详情的故障数量
	public function test_tourdetail_break_nums01(){
		$breaknums = 0 ;
		$this->url = $this->get_url("/tour/detail.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = self::$pub_data['action_detail'];
		$this->parms['ddid'] = self::$onedata;
		$this->parms['city'] = self::$pub_data['city'];
		$this->parms['city_id'] = self::$pub_data['city_id'];
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$breaknums = $this->rlt['data']['breakdown_total'] ;   //将查看到的故障数量赋值
		//print $breaknums ;
		//$this->assertEquals($this->rlt['data']['accident_total'],self::$pub_data['detail']) ;
		//$this->assertEquals($this->rlt['data']['comment_total'],self::$pub_data['detail']) ;
		$this->assertEquals($this->rlt['status'],0) ;
		//$this->assertEquals($this->rlt['data']['feedback_car_total'],self::$pub_data['detail']) ;
		return $breaknums ;
	}

	//用例1：查看故障列表
	//用例2：提交故障
	//用例3：继续查看故障列表
	/** 
     * @dataProvider provider 
    */ 
	public function test_breakdown($type,$result){
		$this->url = $this->get_url("/break-down/upload.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = $type;
		$this->parms['order_id'] = self::$onedata;
		//$this->parms['city_id'] =  self::$pub_data['city_id'];
		switch (self::$index) {
			case '0':
				self::$index += 1 ;
				$this->rlt=$this->post_reslut($this->url,$this->parms);
				self::$asum = count($this->rlt['data']) ;     //判断一个订单有几个事故
				$idx = self::$asum -1 ; 
				//print_r($this->rlt);
				$this->assertEquals($this->rlt['msg'],$result);
				if($idx>0){
					$this->assertEquals($this->rlt['data'][$idx]['order_id'],self::$onedata);
					//$this->assertEquals($this->rlt['data'][$idx]['content'],self::$pub_data['content']);
					//$this->assertEquals($this->rlt['data'][$idx]['break_units'],self::$bu);
				}		
				break;
			case '1':
				self::$index += 1 ;
				$this->parms['break_units'] = self::$bu ;
				$this->parms['content'] = self::$pub_data['content'] ;
 				$this->rlt=$this->post_reslut($this->url,$this->parms);
				//print_r($this->rlt);
				$this->assertEquals($this->rlt['msg'],$result);
				break;
			case '2':
				self::$index += 1 ;
				$this->rlt=$this->post_reslut($this->url,$this->parms);
				$curnum = count($this->rlt['data']) ;   //当前故障的总数
				$idx = $curnum -1 ;   //取所有数组(故障数)中的最后一个索引
				$newnum = self::$asum + 1 ;      //故障多了一个后新的总数
				//print_r($this->rlt);
				$this->assertEquals($curnum,$newnum);
				$this->assertEquals($this->rlt['msg'],$result);
				if($idx>0){
					$this->assertEquals($this->rlt['data'][$idx]['order_id'],self::$onedata);
					$this->assertEquals($this->rlt['data'][$idx]['content'],self::$pub_data['content']);
					$this->assertEquals($this->rlt['data'][$idx]['break_units'],self::$bu);
				}
				break;
			default:
				# code...
				break;
		}
		
	}



	//提交故障后，再次查看故障数量
	/**
	 * @depends test_tourdetail_break_nums01
	 */
	public function test_tourdetail_break_nums02($breaknums){
		$curnum =  $breaknums + 1;
		$this->url = $this->get_url("/tour/detail.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		//$this->parms['action'] = self::$pub_data['action_detail'];
		$this->parms['action'] = tour_action_detail;
		$this->parms['ddid'] = self::$onedata;
		$this->parms['city'] = self::$pub_data['city'];
		$this->parms['city_id'] = self::$pub_data['city_id'];
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		//$this->assertEquals($this->rlt['data']['accident_total'],self::$pub_data['detail']) ;
		//$this->assertEquals($this->rlt['data']['comment_total'],self::$pub_data['detail']) ;
		$this->assertEquals($this->rlt['data']['breakdown_total'],$curnum);
		//$this->assertEquals($this->rlt['data']['feedback_car_total'],self::$pub_data['detail']) ;
		
	}


	public function provider(){
		return array(
			array("list","成功"),
			array("upload","提交成功"),
			array("list","成功")
		);
	}


}