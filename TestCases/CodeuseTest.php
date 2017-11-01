<?php


require_once dirname(__FILE__).'/BaseCase.php';
//require_once 'DBTest.php';

//优惠券兑换历史
class CodeUseTest extends BaseCase{

	private $url;
	private $parms;
	private $url1 ;
	private $parms1 ;
	private static $dataset;
	//private $stack ;
	public static function setUpBeforeClass(){
		//查看历史优惠券 ---/code/history.html
		//使用优惠券    ---/code/use.html
		self::$dataset = array(
			array('22R87X','使用推荐码成功!'),
			array('22R87X','您使用过其他人的推荐码!'),
			array('x1','优惠码不存在！'),
		);

		$id = self::$uid;
		$sql =  "select id from 630_share where uid ='{$id}'";
		$deldata = "delete from 630_share where uid = '{$id}'";
		$info = self::$conn->query($sql);
		//echo "-----------------------";
		//print_r($info->fetch_row());
		$data = $info->fetch_row();
		if($data != null){
			self::$conn->query($deldata);
			//echo "删除成功！！！！！！！！！";
		}
	}



	//用例1：优惠券不存在，查看历史优惠券
	public function test_history_noexist(){
		$this->url = $this->get_url("/code/history.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = 'list';
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt['data']);
		//print_r($this->rlt['status']);
		$this->assertEquals($this->rlt['msg'],'');
		//$randstr = $result['data']['randstr'];
	}


	//用例2：使用别人的优惠券
	public function test_usecode_no(){
		$this->url1 = $this->get_url("/code/use.html");
		$this->parms1  = $this->get_parms();
		$this->parms1['uid'] = self::$uid;
		$this->parms1['key'] = self::$key;
		//echo "KKKKKKKKKKKKK";
		//print_r(self::$dataset[0][0]);
		//print_r(self::$dataset[0][1]);
		$this->parms1['code'] = self::$dataset[0][0];
		$this->rlt1=$this->post_reslut($this->url1,$this->parms1);
		$this->assertEquals($this->rlt1['msg'],self::$dataset[0][1]);
	}

	//用例3：使用优惠券后，查看历史记录
	public function test_history_exist(){
		$this->url = $this->get_url("/code/history.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = 'list';
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt['data']);
		//print_r($this->rlt['status']);
		$this->assertEquals($this->rlt['data'][0]['type'],'user');
		$this->assertEquals($this->rlt['data'][0]['desc'],'使用分享码获得优惠券');
		//$randstr = $result['data']['randstr'];
	}


	//用例4：已使用别人的优惠券，再次使用
	public function test_usecode_yes(){
		$this->url1 = $this->get_url("/code/use.html");
		$this->parms1  = $this->get_parms();
		$this->parms1['uid'] = self::$uid;
		$this->parms1['key'] = self::$key;
		$this->parms1['code'] = self::$dataset[1][0];
		$this->rlt1=$this->post_reslut($this->url1,$this->parms1);
		$this->assertEquals($this->rlt1['msg'],self::$dataset[1][1]);
	}


	//用例5，优惠码不存在
	public function test_codeisnotexist(){
		$this->url1 = $this->get_url("/code/use.html");
		$this->parms1  = $this->get_parms();
		$this->parms1['uid'] = self::$uid;
		$this->parms1['key'] = self::$key;
		$this->parms1['code'] = self::$dataset[2][0];
		$this->rlt1=$this->post_reslut($this->url1,$this->parms1);
		$this->assertEquals($this->rlt1['msg'],self::$dataset[2][1]);
	}


	public  static function tearDownAfterClass(){

	}




}