<?php


require_once dirname(__FILE__).'/BaseCase.php';


class InvoiceTest extends BaseCase{
	//包含：开发票，发票记录，用户信息
	//开发票	    		/invoice/create.html   
	//发票记录				/invoice/record.html   
	//用户信息				/customer/info.html    type = "user"   //主要查看发票金额



	private $url;
	private $parms;
	private static $sum;
	private static $ddid ;
	private static $pub_data ;
	private static $invlimt = 560;    //此值定义不能大于900，因为下面的开发票已经设置最大900
	private static $inv_data ;
	private static $type ;

	public static function setUpBeforeClass(){
		self::$type = rand(1,2); 
		self::$inv_data = array("money"=>"200","title"=>"testa","taxno"=>"123456789987654321","name"=>"nameA","mobile"=>"13210001000","address"=>"酒仙桥A","city_id"=>"1","status"=>"0","result"=>"操作成功");
		self::$pub_data = array("type"=>"user","recodetype"=>"invoice") ;
		$id = self::$uid ;
		$inv = self::$invlimt ;
		$sql = "UPDATE 630_customer set inv_limit = '{$inv}' where id = '{$id}'";
		$rltdata = self::$conn->query($sql);
		//self::$onedata = $rltdata->fetch_row();
	}





	//用例1：开发票各字段的判断
	/** 
     * @dataProvider provider 
    */ 
	public function test_createinvexception($type,$money,$title,$taxno,$name,$mobile,$address,$cityid,$result){
		$this->url = $this->get_url("/invoice/create.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;	
		$this->parms['money'] = $money ;
		$this->parms['title'] = $title ;
		$this->parms['tax_no'] =  $taxno ;
		$this->parms['name'] = $name ;
		$this->parms['mobile'] = $mobile;
		$this->parms['address'] = $address;
		$this->parms['city_id'] = $cityid ;
		$this->parms['type'] = $type;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		$this->assertEquals($this->rlt['msg'],$result);
	}


	//用例2：进行正常开发票
	public function test_createinvoice(){
		$this->url = $this->get_url("/invoice/create.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;	
		$this->parms['money'] = self::$inv_data['money'];
		$this->parms['title'] = self::$inv_data['title'] ;
		$this->parms['tax_no'] =  self::$inv_data['taxno'] ;
		$this->parms['name'] = self::$inv_data['name'];
		$this->parms['mobile'] = self::$inv_data['mobile'];
		$this->parms['address'] = self::$inv_data['address'];
		$this->parms['city_id'] = self::$inv_data['city_id'];
		$this->parms['type'] = self::$type;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['data'],self::$inv_data['result']);
				
	}


	//开发票后查看发票记录
	 ///**
     //* @depends test_createinvoice
     //*/
	public function test_invrecode(){
		$this->url = $this->get_url("/invoice/record.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		//$this->parms['type'] = self::$pub_data['type_info'];
		$this->parms['type'] = self::$pub_data['recodetype'];
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		//print_r($stack[0]) ;
		$this->assertEquals($this->rlt['data'][0]['money'],self::$inv_data['money']);
		$this->assertEquals($this->rlt['data'][0]['name'],self::$inv_data['name']);
		$this->assertEquals($this->rlt['data'][0]['mobile'],self::$inv_data['mobile']);
		$this->assertEquals($this->rlt['data'][0]['address'],self::$inv_data['address']);
		$this->assertEquals($this->rlt['data'][0]['types'],self::$type);
		$this->assertEquals($this->rlt['data'][0]['tax_no'],self::$inv_data['taxno']);
		$this->assertEquals($this->rlt['data'][0]['status'],self::$inv_data['status']);

	}


	//查询可开发票金额
	public function testinvlimit(){
		$this->url = $this->get_url("/customer/info.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		//$this->parms['type'] = self::$pub_data['type_info'];
		$this->parms['type'] = self::$pub_data['type'];
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$invnum = $this->rlt['data']['inv_limit'];
		$this->assertNotEmpty($invnum) ;
		return $invnum ;
	}


	//验证开发票后的金额是否正确
	/**
     * @depends testinvlimit
     */
	public function test_invafter($invnum){
		$curinvnum = self::$invlimt - 200;
		$this->assertEquals($invnum,$curinvnum);
	}



	public function provider(){
		return array(
			array(1,200,"testa","","nameA","13210001000","酒仙桥A",1,"您填写的纳税人识别号不正确,请检查"),						//发票标识号为空
			array(1,200,"testa","123456789987654321","nameA","13210001000","",1,"地址不能为空"),			//地址为空
			array(1,900,"testa","123456789987654321","nameA","13210001000","酒仙桥A",1,"发票金额大于可开发票额度")	//发票金额大于当前发票金额

		);
	}



	public static function tearDownAfterClass(){
		$id = self::$uid ;
		$sql = "DELETE from 630_invoice where uid = '{$id}'";
		$rltdata = self::$conn->query($sql);
	}

}