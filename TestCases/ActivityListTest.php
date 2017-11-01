<?php


require_once dirname(__FILE__).'/BaseCase.php';
//require_once 'DBTest.php';


class ActivityListTest extends BaseCase{
	//包含：活动列表信息，活动分享信息
	//活动列表信息    		/activity/list.html    action = "list"
	//活动分享信息			/share/get-share-contents.html    type=3



	private $url;
	private $parms;
	private static $sum;
	private static $ddid ;
	private static $type ;

	public static function setUpBeforeClass(){
		self::$type = 3 ;
	}



	//用例1：查看当前活动列表
	/** 
     * @dataProvider provider 
    */ 
	public function test_activitylist($type,$result){
		$this->url = $this->get_url("/activity/list.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = $type;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		self::$sum = count($this->rlt['data']) ;     //查询有几条数据
		$idx = self::$sum -1 ; 
		//print_r($this->rlt);
		self::$ddid = $this->rlt['data'][$idx]['id'] ;
		//echo "btv\n\t" ;
		//print_r(self::$ddid);
		if($idx>0){
			if($result==0){
				$this->assertNotNull($this->rlt['data'][$idx]['title']);
				$this->assertEquals($this->rlt['data'][$idx]['status'],1);
				$this->assertNotNull($this->rlt['data'][$idx]['h5_url']);
			}else{
				$index = $this->getvalue('activity','index');
				$title = $this->getvalue('activity','title');
				$this->assertEquals($this->rlt['data'][$index]['title'],$title);	

				$city_id = $this->getvalue('activity','city_id');
				$this->assertEquals($this->rlt['data'][$index]['city_id'],$city_id);

				$h5_url = $this->getvalue('activity','h5_url');
				$this->assertContains($h5_url,$this->rlt['data'][$index]['h5_url']);

				$act_s_time = $this->getvalue('activity','act_s_time');
				$this->assertEquals($this->rlt['data'][$index]['act_s_time'],$act_s_time);

				$act_e_time = $this->getvalue('activity','act_e_time');
				$this->assertEquals($this->rlt['data'][$index]['act_e_time'],$act_e_time);
				
				$join_time = $this->getvalue('activity','join_time');
				$this->assertEquals($this->rlt['data'][$index]['join_time'],$join_time);	
			}
		}		
	}


	//用例2：查看分享活动信息
	/** 
     * @dataProvider provider1 
    */ 
	public function test_cursharecontents($index,$result){
		$this->url = $this->get_url("/share/get-share-contents.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['type'] = self::$type;
		//$index为开关，控制是否要测试特定活动，如果为0，不指定具体活动，如果为1，测试具体活动，测试数据通过TestData.xml中获取
		if($index==0){
			$this->parms['activity_id'] = self::$ddid;
			$this->rlt=$this->post_reslut($this->url,$this->parms);
			//print_r($this->rlt);
			$this->assertNotNull($this->rlt['data']['title']);
			$this->assertNotNull($this->rlt['data']['content']);	
			$this->assertNotNull($this->rlt['data']['url']);	
			$this->assertEquals($this->rlt['data']['need_auth'],$result);	//need_auth是如何查出来的？
		}else
		{
			$ddid = $this->getvalue('sharecontent','ddid') ;
			$title = $this->getvalue('sharecontent','title') ;
			$content = $this->getvalue('sharecontent','content') ;
			$url = $this->getvalue('sharecontent','url') ;
			$this->parms['activity_id'] = $ddid;
			$this->rlt=$this->post_reslut($this->url,$this->parms);
			//print_r($this->rlt);
			$this->assertEquals($this->rlt['data']['title'],$title);
			$this->assertEquals($this->rlt['data']['content'],$content);	
			$this->assertContains($url,$this->rlt['data']['url']);
			$this->assertEquals($this->rlt['data']['need_auth'],$result);	
		}
	
	}

	public function provider(){
		return array(
			array("list",0)
		);
	}

	public function provider1(){
		return array(
			array(0,1)
		);
	}


}