<?php


require_once dirname(__FILE__).'/BaseCase.php';
require_once dirname(__FILE__).'/../CommonClass/ApiParms.php';


class FeedBackTest extends BaseCase{
	//包含：反馈详情上传，故障详情列表
	//反馈详情上传    		/feed-back/upload.html    action=upload
	//反馈详情列表         /feed-back/upload.html     action= list


	private $url;
	private $parms;
	private  $id ;
	public static $orderid;
	private static $index ;
	private static $onedata ;
	private static $pub_data ;
	private static $asum;
	//private static $bu ;

	public static function setUpBeforeClass(){
		self::$pub_data = array("city_id"=>"1","content"=>"test");
		self::$index = 0 ;
		//self::$bu = rand(1,8) ;
		$id = self::$uid ;
		$sql = "select id from 630_tour where customer_id = '{$id}' order by  id desc limit 10" ;
		$rltdata = self::$conn->query($sql);
		self::$onedata = $rltdata->fetch_row()[0];
		//print_r(self::$onedata);
	}



	//第一次查看行程详情的意反馈数量
	public function test_tourdetail_feedback_nums01(){
		$feedbacknums = 0 ;
		$this->url = $this->get_url("/tour/detail.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = tour_action_detail;
		$this->parms['ddid'] = self::$onedata;
		$this->parms['city'] = city ;
		$this->parms['city_id'] = city_id;
		//print_r($this->parms) ;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$feedbacknums = $this->rlt['data']['feedback_car_total'] ;   //将查看到的故障数量赋值
		//echo $feedbacknums ;
		$this->assertEquals($this->rlt['status'],0) ;
		return $feedbacknums ;
	}

	//用例1：查看反馈详情列表信息
	//用例2：进行反馈信息上传
	//用例3：进行再次查看反馈列表
	/** 
     * @dataProvider provider 
    */ 
	public function test_feedback($type,$result){
		$this->url = $this->get_url("/feed-back/upload.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = $type;
		$this->parms['order_id'] = self::$onedata;
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
					$this->assertEquals($this->rlt['data'][$idx]['content'],self::$pub_data['content']);
					//$this->assertEquals($this->rlt['data'][$idx]['break_units'],self::$bu);
				}		
				break;
			case '1':
				self::$index += 1 ;
				$this->parms['content'] = self::$pub_data['content'] ;
 				$this->rlt=$this->post_reslut($this->url,$this->parms);
				///print_r($this->rlt);
				$this->assertEquals($this->rlt['msg'],$result);
				break;
			case '2':
				self::$index += 1 ;
				$this->rlt=$this->post_reslut($this->url,$this->parms);
				$curnum = count($this->rlt['data']) ;   //当前意见反馈的总数
				$idx = $curnum -1 ;   //取所有数组(反馈数)中的最后一个索引
				$newnum = self::$asum + 1 ;      //意见反馈多了一个后新的总数
				//print_r($this->rlt);
				$this->assertEquals($curnum,$newnum);
				$this->assertEquals($this->rlt['msg'],$result);
				if($idx>0){
					$this->assertEquals($this->rlt['data'][$idx]['order_id'],self::$onedata);
					$this->assertEquals($this->rlt['data'][$idx]['content'],self::$pub_data['content']);
				}
				break;
			default:
				# code...
				break;
		}
		
	}


	/**
	 	* @depends test_tourdetail_feedback_nums01
	 */
	public function test_tourdetail_feedback_nums02($feedbacknums){
		$curnum =  $feedbacknums + 1;
		$this->url = $this->get_url("/tour/detail.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = tour_action_detail;
		$this->parms['ddid'] = self::$onedata;
		$this->parms['city'] = city ;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['data']['feedback_car_total'],$curnum);
	}


	public function provider(){
		return array(
			array("list","成功"),
			array("upload","提交成功"),
			array("list","成功")
		);
	}


}