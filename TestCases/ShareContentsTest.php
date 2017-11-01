<?php


require_once dirname(__FILE__).'/BaseCase.php';
require_once dirname(__FILE__).'/../CommonClass/get_data.php';



class ShareContentsTest extends BaseCase{
	//包含：活动分享信息
	//活动分享信息    		/share/get-share-contents.html    



	private $url;
	private $parms;
	private $content ;
	private static $arr0;
	private static $arr1;
	private static $arr2;
	private static $arr3;
	private static $arr4;
	private static $arr5;
	private static $dd_id ;

	public static function setUpBeforeClass(){
		//type=1
		self::$arr0 = array("title"=>"一度用车分享码，输入即送30元！","url"=>"http://test2.yiduyongche.com/share/share-code.html","desc"=>"分享给好友，TA在“优惠”输入，兑换成功即可获得3张10元优惠券。TA首次使用车后(有效里程≥5公里)，您也将获得3张10元优惠券奖励。","need_auth"=>"0");
		//type=2
		self::$arr1 = array("title"=>"一度用车行程","content"=>"使用一度电动汽车，便捷、健康又环保！","url"=>"http://test2.yiduyongche.com/share/info.html","need_auth"=>"0");
		//type=4
		self::$arr2 = array("title"=>"一度优惠不停歇，优惠券抢不停！","content"=>"一度优惠券，挚友专享，是朋友我才告诉你！","url"=>"http://www.yiduyongche.com/weixinpay/get-weixin-code.html","need_auth"=>"0");
		//type=6
		self::$arr3 = array("need_auth"=>"0");
		//type=7
		self::$arr4 = array("title"=>"O2O就是这么玩","content"=>"分享红包太无聊,换个姿势来一次","url"=>"http://test2.yiduyongche.com/share/ar.html","need_auth"=>"0");
		//type=8
		self::$arr5 = array("title"=>"一度用车用户指南","content"=>"如何用车、如何收费、如何取还车······你想知道的都在这里······","url"=>"http://test2.yiduyongche.com/user-guide/index.html","need_auth"=>"0");
		self::$dd_id = get_tour_id(self::$conn,self::$uid);
	}



	//用例1：查看活动分享信息
	/** 
     * @dataProvider provider 
    */ 
	public function test_sharecontents($type,$result){
		$this->url = $this->get_url("/share/get-share-contents.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['type'] = $type;
		if($type==2||$type==4){
			$this->parms['dd_id'] = self::$dd_id;
		}
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);	
		switch ($result) {
			case '0':     //type = 1
				$code = get_user_code(self::$conn,self::$uid) ;
				//echo "code::".$code ;
				$this->content = '一度用车分享码“'.$code.'”' ;
				//echo "$content::::".$this->content ;
				$this->assertEquals($this->rlt['data']['title'],self::$arr0['title']);
				$this->assertEquals($this->rlt['data']['content'],$this->content);
				$this->assertContains(self::$arr0['url'],$this->rlt['data']['url']);
				$this->assertEquals($this->rlt['data']['desc'],self::$arr0['desc']);
				//$this->assertEquals($this->rlt['data']['need_auth'],self::$arr0['need_auth']);		
				break;
			case '1':    //type =2 
				$this->assertEquals($this->rlt['data']['title'],self::$arr1['title']);
				$this->assertEquals($this->rlt['data']['content'],self::$arr1['content']);
				$this->assertContains(self::$arr1['url'],$this->rlt['data']['url']);
				//$this->assertEquals($this->rlt['data']['need_auth'],self::$arr1['need_auth']);	
				break;
			case '2':   //type=4
				$this->assertEquals($this->rlt['data']['title'],self::$arr2['title']);
				$this->assertEquals($this->rlt['data']['content'],self::$arr2['content']);
				$this->assertContains(self::$arr2['url'],$this->rlt['data']['url']);
				//$this->assertEquals($this->rlt['data']['need_auth'],self::$arr2['need_auth']);	
				break;
			case '3':   //type=6
				//$this->assertEquals($this->rlt['data']['need_auth'],self::$arr3['need_auth']);	
				break;
			case '4':   //type=7
				$this->assertEquals($this->rlt['data']['title'],self::$arr4['title']);
				$this->assertEquals($this->rlt['data']['content'],self::$arr4['content']);
				$this->assertContains(self::$arr4['url'],$this->rlt['data']['url']);
				//$this->assertEquals($this->rlt['data']['need_auth'],self::$arr4['need_auth']);	
				break;
			case '5':   //type=8
				$this->assertEquals($this->rlt['data']['title'],self::$arr5['title']);
				$this->assertEquals($this->rlt['data']['content'],self::$arr5['content']);
				$this->assertContains(self::$arr5['url'],$this->rlt['data']['url']);
				//$this->assertEquals($this->rlt['data']['need_auth'],self::$arr5['need_auth']);	
				break;
			default:
				# code...
				break;
		}	
	}


	public function provider(){
		return array(
			array(1,'0'),
			array(2,'1'),
			array(4,'2'),
			array(6,'3'),
			array(7,'4'),
			array(8,'5')
		);
	}


}