<?php

//namespace TestCases;

//require_once 'CommonClass/Require.php';
class BaseCase extends PHPUnit_Framework_TestCase{
	public $gc;
	public $cp;
	public  $randstr;
	public $result;
	public $doc ;                                        
	public $urls=array("http://test_new.yiduyongche.com","http://ceshi.yiduyongche.com");
	public $params=array('channel' => 'AppStore', 'wei' => 39.98224485598885,"clientversion" => "ios_3002","device_name" => "iPhone6 Plus","jing" => 116.5003987571553,"device_id" => "1679ff0411106a429e0a43ae04fda9010994bd39","systemversion" => "10.2");
	public  static $key;
	public  static $mobile;
	public  static $uid;
	public  static $code;
	private $db;
	public static $conn = null;


	

	public function get_url($apiname,$index=0)
	{
		return $this->urls[$index]. "" .$apiname;
	}


	//public function 


	public function get_parms()
	{
		return $this->params;
	}



	public function print_log($casename,$apiname,$rslt,$status=0){
		//print_r($rslt['status']) ;
		if($rslt['status'] !=  $status){
			echo "代码存在问题或已被修改=>" ;
			echo $casename."::".$apiname."::" ;
			print_r($rslt) ;
		}
	}




	//post方式的请求
	public  function post_reslut($url,$post_params){
		$ch = curl_init();
		//curl_setopt();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_params);
		$data = curl_exec($ch);
		curl_close($ch);
		$result=json_decode($data,true);
		//print_r($result);
		return $result;
	}

	//读取xml文件
	public function getvalue($type,$node){
		$this->doc = new DOMDocument();
		$this->doc->load("TestData/TestData.xml");
		foreach ($this->doc->getElementsByTagName($type) as $item) {
			$list = $item->getElementsByTagName($node);
			foreach ($list as $list1) {
				$value = $list1->nodeValue;
				break;
			}
		}
		return $value;
	}






	public function getConnection(){
		$hostname = $this->getvalue('db','hostname');
		$username = $this->getvalue('db','username');
		$password = $this->getvalue('db','password');
		$dbname = $this->getvalue('db','dbname');
		if($this->db == null){
			//$this->getvalue('phone')
			$this->db = new mysqli($hostname,$username,$password,$dbname);
		}
		//$this->db->close();
		return $this->db;
	}


}