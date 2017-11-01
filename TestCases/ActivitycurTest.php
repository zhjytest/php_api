<?php


require_once dirname(__FILE__).'/BaseCase.php';



class ActivityCurTest extends BaseCase{
	//包含：当前活动信息
	//当前活动信息    		/activity/cur.html    action = "cur"



	private $url;
	private $parms;
	private static $sum;

	// public static function setUpBeforeClass(){
	// }



	//用例1：查看当前活动信息
	/** 
     * @dataProvider provider 
    */ 
	public function test_activitylist($type,$result){
		$this->url = $this->get_url("/activity/cur.html");
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = $type;
		$this->parms['city_id'] = 1;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		self::$sum = count($this->rlt['data']) ;     //查询有几条数据
		$idx = self::$sum -1 ; 
		//print_r($this->rlt);
		if($idx>0){
			if($result==0){
				$this->assertNotNull($this->rlt['data'][$idx]['title']);
				//$this->assertEquals($this->rlt['data'][$idx]['status'],1);
				$this->assertNotNull($this->rlt['data'][$idx]['h5_url']);
			}else{
				$index = $this->getvalue('activity','index');
				$title = $this->getvalue('activity','title');
				$this->assertEquals($this->rlt['data'][$index]['title'],$title);	

				$city_id = $this->getvalue('activity','city_id');
				$this->assertEquals($this->rlt['data'][$index]['city_id'],$city_id);
				
				$join_time = $this->getvalue('activity','join_time');
				$this->assertEquals($this->rlt['data'][$index]['join_time'],$join_time);	
			}
		}		
	}


	public function provider(){
		return array(
			array("cur",0)
		);
	}


}